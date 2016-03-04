
import java.awt.Window;
import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.math.BigInteger;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;
import java.util.logging.Level;
import java.util.logging.Logger;

public class Utility {

    private static ExecutorService executor;
    private static String dbName;
    private static String dbUsername;
    private static String scriptURL;
    private static String dbPassword;

    /**
     * Gets an existing cached thread pool or creates new one if none exists
     *
     * @return
     */
    public static ExecutorService getExecutor() {
        if (executor == null) {
            executor = Executors.newCachedThreadPool();
        }
        return executor;
    }

    /**
     * Writes a throwable to log
     *
     * @param e
     */
    public static void writeLog(Throwable e) {
        Logger.getLogger(Utility.class.getName()).log(Level.SEVERE, null, e);
    }

    /**
     * Centres a child window on a parent window
     *
     * @param parent
     * @param child
     */
    public static void centreOnParent(Window parent, Window child) {
//        if (parent == null) {
//            centreOnScreen(child);
//        } else {
//            Point parentLocationOnScreen = parent.getLocationOnScreen();
//            int x = (int) (parent.getWidth() - child.getWidth()) / 2;
//            int y = (int) (parent.getHeight() - child.getHeight()) / 2;
//            x += parentLocationOnScreen.x;
//            y += parentLocationOnScreen.y;
//            //Make sure the top of child doesn't fall behind the screen
//            x = x < 0 ? 0 : x;
//            y = y < 0 ? 0 : y;
//
//            child.setLocation(x, y);
//        }
        child.setLocationRelativeTo(parent);
    }

    /**
     * Centres a window to screen
     *
     * @param frame
     */
    public static void centreOnScreen(Window frame) {
//        Dimension dimension = Toolkit.getDefaultToolkit().getScreenSize();
//        int x = (int) ((dimension.getWidth() - frame.getWidth()) / 2.0D);
//        int y = (int) ((dimension.getHeight() - frame.getHeight()) / 2.0D);
//        frame.setLocation(x, y);
        frame.setLocationRelativeTo(null);
    }

    public static String getAppName() {
        return "Foo Enterprise";
    }

    /**
     * Encrypts a message using sha-1 algorithm
     *
     * @param message encrypted message
     * @return
     * @throws NoSuchAlgorithmException
     */
    public static String encrypt(String message) throws NoSuchAlgorithmException {
        MessageDigest md = MessageDigest.getInstance("sha-1");
        byte[] digest = md.digest(message.getBytes());
        BigInteger bigInteger = new BigInteger(digest);
        return bigInteger.toString(16);
    }

    /**
     * initialise variable from ini file
     *
     * @throws FileNotFoundException
     * @throws IOException
     */
    public static void initFromFile() throws FileNotFoundException, IOException {
        try (BufferedReader br = new BufferedReader(new FileReader(new File("biometrics.ini")))) {
            while (br.ready()) {
                String line = br.readLine();
                if (line.matches("[^#;]\\S+\\s*=[\\s\\S]*")) {
                    line = line.replaceAll("\\s", "");
                    String[] split = line.split("=");
                    if (split.length > 1) {
                        switch (split[0].toLowerCase()) {
                            case "script_url":
                                scriptURL = split[1];
                                break;
                            case "db_name":
                                dbName = split[1];
                                break;
                            case "db_username":
                                dbUsername = split[1];
                                break;
                            case "db_password":
                                dbPassword = split[1];
                                break;
                        }
                    }
                }
            }
        }
    }

    /**
     * Gets URL of server script. This should be called after
     * {@code initFromFile()} has been called
     *
     * @return
     */
    public static String getScriptURL() {
        if (scriptURL == null) {
            return "";
        } else {
            return scriptURL;
        }
    }

    /**
     * Gets database name. This should be called after {@code initFromFile()}
     * has been called
     *
     * @return
     */
    public static String getDBName() {
        if (dbName == null) {
            return "";
        } else {
            return dbName;
        }
    }

    /**
     * Gets database username. This should be called after
     * {@code initFromFile()} has been called
     *
     * @return
     */
    public static String getDBUsername() {
        if (dbUsername == null) {
            return "";
        } else {
            return dbUsername;
        }
    }

    /**
     * Gets database password. This should be called after
     * {@code initFromFile()} has been called
     *
     * @return
     */
    public static String getDBPassword() {
        if (dbPassword == null) {
            return "";
        } else {
            return dbPassword;
        }
    }

}
