
import java.io.ByteArrayOutputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.math.BigInteger;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;

/**
 *
 * @author Victor Anuebunwa
 */
public class WebConnection {

    private Status s;
    private String staffID;
    private static WebConnection thisClass;

    /**
     * Enumeration representing types of reply sent from this application
     */
    public enum ReplyType {

        PASSED,
        FAILED
    }

    /**
     * Enumeration representing types of response received from server
     */
    public enum ResponseType {

        OK,
        FAILED,
        ERROR
    }

    /**
     * Get an instance of this class, new instance is created if it does not
     * exists
     *
     * @return a {@link WebConnection} object
     */
    public static WebConnection getInstance() {
        if (thisClass == null) {
            thisClass = new WebConnection();
        }
        return thisClass;
    }

    /**
     * Queries the server to obtain the current status of this staff
     *
     * @param staffID staff ID
     * @throws IllegalArgumentException if an unknown message is received from
     * server
     * @throws IOException if error occurs during communication with server
     */
    public void queryServer(String staffID) throws IllegalArgumentException, IOException {
        this.staffID = staffID;
        String query = "REQUEST:STATUS:" + staffID;
        InputStream post = post(query);
        ByteArrayOutputStream bo = new ByteArrayOutputStream();
        byte b[] = new byte[1024];
        int n;
        while ((n = post.read(b)) != -1) {
            bo.write(b, 0, n);
        }
        String response = new String(bo.toByteArray());
        processResponse(response);
    }

    /**
     * Process request received from server
     *
     * @param response
     * @throws IllegalArgumentException if an unknown message is received from
     * server
     */
    private void processResponse(String response) throws IllegalArgumentException {
        System.out.println("Server responsed " + response);
        if (response.contains(":")) {
            String[] split = response.split(":");
            try {
                ResponseType r = Enum.valueOf(ResponseType.class, split[0].toUpperCase());
                switch (r) {
                    case OK:
                        s = Enum.valueOf(Status.class, split[1].toUpperCase());
                        break;
                    case FAILED:
                        s = Status.NULL;
                        break;
                    case ERROR:
                        s = Status.NULL;
                        throw new IllegalArgumentException(split[1]);
                    default:
                        throw new AssertionError(r.name());
                }
            } catch (IllegalArgumentException e) {
                s = Status.NULL;
            }
        } else {
            s = Status.NULL;
        }
    }

    private static URL getScriptURL() throws MalformedURLException {
        return new URL(Utility.getScriptURL());
    }

    /**
     * Make a post request to server with the given query
     *
     * @param query
     * @return an InputStream
     * @throws IOException if error occurs during communication with server
     */
    private static InputStream post(String query) throws IOException {
        query = query.toLowerCase();
        try {
            final HttpURLConnection connection = (HttpURLConnection) getScriptURL().openConnection();
            connection.setRequestMethod("GET");
            connection.setReadTimeout(10000); //10 secs
            connection.setRequestProperty("User-Agent", "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.95 Safari/537.11");
            connection.setRequestProperty("Accept-Language", "en-US,en;q=0.5");
            connection.setDoOutput(true);
            final String urlParameters = "q=" + escape2URIParam(query);
            try (final DataOutputStream wr = new DataOutputStream(connection.getOutputStream())) {
                wr.writeBytes(urlParameters);
                wr.flush();
            }
            return connection.getInputStream();
        } catch (IOException e) {
            throw e;
        }
    }

    private static String escape2URIParam(final String s) {
        final StringBuilder sb = new StringBuilder();
        for (int n = s.length(), i = 0; i < n; ++i) {
            final char c = s.charAt(i);
            if (String.valueOf(c).matches("[\\w-.~]")) {
                sb.append(c);
            } else {
                final int ci = c;
                final String code = new BigInteger(String.valueOf(ci)).toString(16);
                sb.append("%");
                sb.append(code);
            }
        }
        return sb.toString();
    }

    /**
     * Gets the staff status returned from server
     *
     * @return current staff Status
     */
    public Status getStatus() {
        return s;
    }

    /**
     * Gets the staff id on which current requests are been made on
     *
     * @return current staff id
     */
    public String getStaffID() {
        return staffID;
    }

    /**
     * Sends a reply to server
     *
     * @param type either of {@link ReplyType}.PASSED or
     * {@link ReplyType}.FAILED
     * @return boolean true if reply was successfully sent else false.
     * @throws java.io.IOException if error occurs during communication with
     * server
     */
    public boolean sendReply(ReplyType type) throws IOException {
        String query;
        query = "REPLY:" + type.name() + ":" + staffID;
        InputStream post = post(query);
        ByteArrayOutputStream bo = new ByteArrayOutputStream();
        byte b[] = new byte[1024];
        int n;
        while ((n = post.read(b)) != -1) {
            bo.write(b, 0, n);
        }
        String response = new String(bo.toByteArray());
        System.out.println("Server responsed " + response);
        return response.startsWith("OK");
    }

}
