
import com.digitalpersona.onetouch.DPFPFingerIndex;
import com.digitalpersona.onetouch.DPFPGlobal;
import com.digitalpersona.onetouch.DPFPTemplate;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.EnumMap;

/**
 *
 * @author Victor Anuebunwa
 */
public class DatabaseImpl {

    private static Connection con;
    private static Statement statement;
    private static DatabaseImpl thisClass;

    private DatabaseImpl() throws SQLException {
        getConnection();
    }

    /**
     * Returns an instance of this class, this enforces the singleton property
     * on this class
     *
     * @return existing or new instance of this class
     * @throws SQLException if a database access error occurs
     */
    public static DatabaseImpl getInstance() throws SQLException {
        if (thisClass == null) {
            thisClass = new DatabaseImpl();
        }
        return thisClass;
    }

    /**
     * Executes a select query
     *
     * @param query
     * @return {@link ResultSet} containing selected data
     * @throws SQLException if a database access error occurs, this method is
     * called on a closed Statement, the given SQL statement produces anything
     * other than a single ResultSet object, the method is called on a
     * PreparedStatement or CallableStatement
     */
    public ResultSet executeQuery(String query) throws SQLException {
        ResultSet rs = statement.executeQuery(query);
        getConnection().commit();
        return rs;
    }

    /**
     * Executes a DDL or DML query
     *
     * @param query
     * @throws SQLException if a database access error occurs, this method is
     * called on a closed Statement, the given SQL statement produces anything
     * other than a single ResultSet object, the method is called on a
     * PreparedStatement or CallableStatement
     */
    public void executeUpdate(String query) throws SQLException {
        statement.executeUpdate(query);
        getConnection().commit();
    }

    /**
     * Returns a connection to the database, creates one if it does not exists
     * already
     *
     * @return a connection to database
     * @throws SQLException if a database access error occurs
     */
    public final Connection getConnection() throws SQLException {
        if (con == null || con.isClosed()) {
            con = DriverManager.getConnection("jdbc:mysql://localhost/biometrics_site", "root", "");
            statement = con.createStatement();
            con.setAutoCommit(false);
        }
        return con;
    }

    /**
     * Returns a {@link EnumMap} containing a staff's fingerprint templates
     *
     * @param staffID
     * @return an {@link EnumMap} mapping each finger index to a template
     * @throws SQLException if a database access error occurs
     * @throws IOException if error occurs while reading data
     */
    public EnumMap<DPFPFingerIndex, DPFPTemplate> getFingerPrint(String staffID) throws SQLException, IOException {
        String query = "select * from fingerprint where Staff_id = '" + staffID + "'";
        ResultSet rs = executeQuery(query);
        EnumMap<DPFPFingerIndex, DPFPTemplate> template = new EnumMap<>(DPFPFingerIndex.class);
        if (rs.next()) {
            for (DPFPFingerIndex finger : DPFPFingerIndex.values()) {
                DPFPTemplate newTemplate = getTemplate(rs, finger);
                template.put(finger, newTemplate);
            }
        }
        return template;
    }

    private DPFPTemplate getTemplate(ResultSet rs, DPFPFingerIndex finger) throws IOException, SQLException {
        DPFPTemplate newTemplate = DPFPGlobal.getTemplateFactory().createTemplate();
        ByteArrayInputStream bArray = (ByteArrayInputStream) rs.getBinaryStream(finger.name());
        ByteArrayOutputStream bo = new ByteArrayOutputStream();

        byte b[] = new byte[1024];
        int n;
        while ((n = bArray.read(b)) != -1) {
            bo.write(b, 0, n);
        }
        newTemplate.deserialize(bo.toByteArray());
        return newTemplate;
    }

    /**
     * registers a fingerprint Map for a staff to database
     *
     * @param staffID
     * @param template
     * @throws SQLException if a database access error occurs
     */
    public void registerStaffFingerprint(String staffID, EnumMap<DPFPFingerIndex, DPFPTemplate> template) throws SQLException {

        StringBuilder sb = new StringBuilder("insert into fingerprint set staff_id = '" + staffID + "', ");
        DPFPFingerIndex[] values = DPFPFingerIndex.values();
        for (int i = 0; i < values.length; i++) {
            sb.append(values[i].name());
            sb.append("=");
            sb.append("?");
            if (i < values.length - 1) {
                sb.append(", ");
            }
        }

        PreparedStatement ps = getConnection().prepareStatement(sb.toString());
        for (int i = 0; i < values.length; i++) {
            DPFPTemplate temp = template.get(values[i]);
            if (temp != null) {
                ps.setBinaryStream(i + 1, new ByteArrayInputStream(temp.serialize()));
            } else {
                ps.setBinaryStream(i + 1, new ByteArrayInputStream("".getBytes()));
            }
        }
        ps.executeUpdate();

    }
}
