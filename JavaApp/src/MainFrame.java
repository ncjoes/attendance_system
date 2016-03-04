
import com.digitalpersona.onetouch.DPFPFingerIndex;
import com.digitalpersona.onetouch.DPFPTemplate;
import com.yellowbambara.fingerprint.FingerPrintReaderDialog;
import com.yellowbambara.fingerprint.Mode;
import java.awt.HeadlessException;
import java.awt.Image;
import java.awt.Toolkit;
import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URI;
import java.net.URISyntaxException;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.EnumMap;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.swing.ImageIcon;
import javax.swing.JOptionPane;

/**
 *
 * @author Victor Anuebunwa
 */
public class MainFrame extends javax.swing.JFrame {

    private Status mode;
    private String staffID;
    private final WebConnection connection;

    /**
     * Creates new form MainFrame
     */
    public MainFrame() {
        initComponents();
        setTitle(Utility.getAppName());

        reset();
        connection = WebConnection.getInstance();
    }

    /**
     * Clears staff existing data and starts a new connection to server
     */
    public void connect() {
        reset();
        connectButton.setEnabled(false);
        Runnable runnable = () -> {
            try {
                String s = "";
                s = JOptionPane.showInputDialog(this, "Enter Staff ID", s);
                try {
                    setBusy(true, "Waiting for response from server...");
                    setStaff(s);
                    connection.queryServer(staffID);
                    setMode(connection.getStatus());
                } catch (IllegalArgumentException | SQLException e) {
                    JOptionPane.showMessageDialog(this, "Oops! Something went wrong\n"
                            + "Reason:\n"
                            + e.getMessage());
                }
            } catch (IOException ex) {
                Logger.getLogger(MainFrame.class.getName()).log(Level.SEVERE, null, ex);
                JOptionPane.showMessageDialog(this, "Oops! Something went wrong\n"
                        + "Check connectivity and try again later");
            }
            setBusy(false, "");
            connectButton.setEnabled(true);
        };
        Utility.getExecutor().execute(runnable);
    }

    private void setStaff(String staffID) throws IllegalArgumentException, SQLException {
        if (staffID != null) {
            this.staffID = staffID;
            String query = "select first_name, last_name, other_names, pic_url from staff where staff_id = '" + staffID + "'";
            try {
                ResultSet rs = DatabaseImpl.getInstance().executeQuery(query);
                if (rs.next()) {
                    staffIDLabel.setText(staffID);
                    firstNameLabel.setText(rs.getString("first_name"));
                    lastNameLabel.setText(rs.getString("last_name"));
                    otherNamesLabel.setText(rs.getString("other_names"));
                    ImageIcon icon;
                    //Set picture
                    try {
                        URI pictureURI = new URI(rs.getString("pic_url"));
                        Image img = Toolkit.getDefaultToolkit().createImage(pictureURI.toURL());
                        icon = new ImageIcon(img);
                        if (icon.getIconHeight() > 120 || icon.getIconWidth() > 120) {
                            img = img.getScaledInstance(120, 120, Image.SCALE_FAST);
                            icon = new ImageIcon(img);
                        }
                    } catch (MalformedURLException | URISyntaxException | IllegalArgumentException ex) {
                        icon = new ImageIcon(getClass().getResource("resources/profile.png"));
                        Logger.getLogger(MainFrame.class.getName()).log(Level.SEVERE, null, ex);
                    }
                    picLabel.setIcon(icon);
                } else {
                    throw new IllegalArgumentException("Staff ID " + staffID + " does not exist");
                }
            } catch (SQLException e) {
                Utility.writeLog(e);
                throw new SQLException("Connection failed, check network connection", e);
            }
        } else {
            throw new IllegalArgumentException("Staff ID not set");
        }
    }

    private void readFingerPrint() {
        Runnable runnable = () -> {
            try {
                switch (mode) {
                    case VERIFICATION:
                        verify();
                        break;
                    case REGISTRATION:
                        enrol();
                        break;
                    case NULL:
                        JOptionPane.showMessageDialog(null, "No session was created for staff " + staffID);
                        break;
                    default:
                        throw new AssertionError("Invalid mode");
                }
            } catch (IOException e) {
                Logger.getLogger(MainFrame.class.getName()).log(Level.SEVERE, null, e);
                JOptionPane.showMessageDialog(this, "Oops! Something went wrong\n"
                        + "Response was not sent to server");
            } catch (HeadlessException | AssertionError e) {
                JOptionPane.showMessageDialog(this, "Oops! Something went wrong");
                Logger.getLogger(MainFrame.class.getName()).log(Level.SEVERE, null, e);
            }
        };
        Utility.getExecutor().execute(runnable);
    }

    private void verify() throws IOException {
        try {
            EnumMap<DPFPFingerIndex, DPFPTemplate> fingerPrint = DatabaseImpl.getInstance().getFingerPrint(staffID);
            FingerPrintReaderDialog dialog = new FingerPrintReaderDialog(this,
                    Mode.VERIFICATION, fingerPrint);
            boolean verified = dialog.isVerified();
            setVerified(verified);
            try {
                connection.sendReply(verified ? WebConnection.ReplyType.PASSED : WebConnection.ReplyType.FAILED);
            } catch (IOException e) {
                setVerified(false);
                throw e;
            }
        } catch (IOException | SQLException ex) {
            setVerified(false);
            connection.sendReply(WebConnection.ReplyType.FAILED);
            Logger.getLogger(MainFrame.class.getName()).log(Level.SEVERE, null, ex);
            JOptionPane.showMessageDialog(this, "Oops! Something went wrong");
        }
    }

    private void enrol() throws IOException {
        FingerPrintReaderDialog dialog = new FingerPrintReaderDialog(this, Mode.ENROLLMENT);
        EnumMap<DPFPFingerIndex, DPFPTemplate> template = dialog.getTemplates();
        if (!template.isEmpty()) {
            try {
                DatabaseImpl instance = DatabaseImpl.getInstance();
                instance.registerStaffFingerprint(staffID, template);
                instance.getConnection().commit();
                setVerified(true);
                connection.sendReply(WebConnection.ReplyType.PASSED);
            } catch (SQLException ex) {
                setVerified(false);
                connection.sendReply(WebConnection.ReplyType.FAILED);
                Logger.getLogger(MainFrame.class.getName()).log(Level.SEVERE, null, ex);
                JOptionPane.showMessageDialog(this, "Oops! Something went wrong");
            }
        } else {
            setVerified(false);
            connection.sendReply(WebConnection.ReplyType.FAILED);
        }
    }

    private void setMode(Status mode) {
        this.mode = mode;
        switch (mode) {
            case VERIFICATION:
                fingerprintButton.setEnabled(true);
                fingerPrintLabel.setIcon(new ImageIcon(getClass().getResource("resources/biometrics.png")));
                break;
            case REGISTRATION:
                fingerprintButton.setEnabled(true);
                fingerPrintLabel.setIcon(new ImageIcon(getClass().getResource("resources/biometrics_enrol.png")));
                break;
            case NULL:
                fingerprintButton.setEnabled(false);
                JOptionPane.showMessageDialog(this, "No session was created for staff " + staffID);
                break;
            default:
                throw new AssertionError(mode.name());
        }
    }

    private void setVerified(boolean status) {
        if (status == true) {
            switch (mode) {
                case VERIFICATION:
                    fingerPrintLabel.setIcon(new ImageIcon(getClass().getResource("resources/biometrics_passed.png")));
                    break;
                case REGISTRATION:
                    fingerPrintLabel.setIcon(new ImageIcon(getClass().getResource("resources/biometrics_enrol_passed.png")));
                    break;
                default:
                    throw new AssertionError(mode.name());
            }
        } else {
            switch (mode) {
                case VERIFICATION:
                    fingerPrintLabel.setIcon(new ImageIcon(getClass().getResource("resources/biometrics_failed.png")));
                    break;
                case REGISTRATION:
                    fingerPrintLabel.setIcon(new ImageIcon(getClass().getResource("resources/biometrics_enrol_failed.png")));
                    break;
                default:
                    throw new AssertionError(mode.name());
            }
        }
    }

    private void setBusy(boolean state, String message) {
        loadLabel.setText(message);
        loadLabel.setVisible(state);
    }

    private void reset() {
        staffID = "";
        picLabel.setIcon(new ImageIcon(getClass().getResource("resources/profile.png")));
        fingerPrintLabel.setIcon(new ImageIcon(getClass().getResource("resources/biometrics.png")));

        staffIDLabel.setText("Not Available");
        lastNameLabel.setText("Not Available");
        firstNameLabel.setText("Not Available");
        otherNamesLabel.setText("Not Available");

        fingerprintButton.setEnabled(false);
        setBusy(false, "");
    }

    @SuppressWarnings("unchecked")
    // <editor-fold defaultstate="collapsed" desc="Generated Code">//GEN-BEGIN:initComponents
    private void initComponents() {

        jPanel1 = new javax.swing.JPanel();
        jPanel2 = new javax.swing.JPanel();
        jLabel66 = new javax.swing.JLabel();
        picLabel = new javax.swing.JLabel();
        jLabel65 = new javax.swing.JLabel();
        jLabel77 = new javax.swing.JLabel();
        jLabel67 = new javax.swing.JLabel();
        staffIDLabel = new javax.swing.JLabel();
        otherNamesLabel = new javax.swing.JLabel();
        lastNameLabel = new javax.swing.JLabel();
        firstNameLabel = new javax.swing.JLabel();
        jPanel3 = new javax.swing.JPanel();
        fingerPrintLabel = new javax.swing.JLabel();
        fingerprintButton = new javax.swing.JButton();
        loadLabel = new javax.swing.JLabel();
        connectButton = new javax.swing.JButton();

        setDefaultCloseOperation(javax.swing.WindowConstants.EXIT_ON_CLOSE);
        setResizable(false);

        jPanel1.setBackground(new java.awt.Color(255, 255, 255));

        jPanel2.setOpaque(false);

        jLabel66.setFont(new java.awt.Font("Tahoma", 1, 12)); // NOI18N
        jLabel66.setText("First Name:");

        picLabel.setBackground(new java.awt.Color(102, 204, 255));
        picLabel.setIcon(new javax.swing.ImageIcon(getClass().getResource("/resources/profile.png"))); // NOI18N
        picLabel.setOpaque(true);

        jLabel65.setFont(new java.awt.Font("Tahoma", 1, 12)); // NOI18N
        jLabel65.setText("Surname:");

        jLabel77.setFont(new java.awt.Font("Tahoma", 1, 12)); // NOI18N
        jLabel77.setText("Staff ID:");

        jLabel67.setFont(new java.awt.Font("Tahoma", 1, 12)); // NOI18N
        jLabel67.setText("Other Names:");

        staffIDLabel.setText("Not Available");

        otherNamesLabel.setText("Not Available");

        lastNameLabel.setText("Not Available");

        firstNameLabel.setText("Not Available");

        javax.swing.GroupLayout jPanel2Layout = new javax.swing.GroupLayout(jPanel2);
        jPanel2.setLayout(jPanel2Layout);
        jPanel2Layout.setHorizontalGroup(
            jPanel2Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(jPanel2Layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(jPanel2Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                    .addComponent(otherNamesLabel, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                    .addComponent(jLabel67, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                    .addGroup(javax.swing.GroupLayout.Alignment.TRAILING, jPanel2Layout.createSequentialGroup()
                        .addComponent(picLabel)
                        .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED, 9, Short.MAX_VALUE)
                        .addGroup(jPanel2Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING, false)
                            .addComponent(staffIDLabel, javax.swing.GroupLayout.Alignment.TRAILING, javax.swing.GroupLayout.DEFAULT_SIZE, 197, Short.MAX_VALUE)
                            .addComponent(jLabel77, javax.swing.GroupLayout.Alignment.TRAILING, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)))
                    .addGroup(javax.swing.GroupLayout.Alignment.TRAILING, jPanel2Layout.createSequentialGroup()
                        .addGroup(jPanel2Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                            .addComponent(lastNameLabel, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                            .addComponent(jLabel65, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE))
                        .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                        .addGroup(jPanel2Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING, false)
                            .addComponent(firstNameLabel, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                            .addComponent(jLabel66, javax.swing.GroupLayout.DEFAULT_SIZE, 197, Short.MAX_VALUE))))
                .addContainerGap())
        );
        jPanel2Layout.setVerticalGroup(
            jPanel2Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(jPanel2Layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(jPanel2Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                    .addComponent(picLabel, javax.swing.GroupLayout.PREFERRED_SIZE, 120, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addGroup(jPanel2Layout.createSequentialGroup()
                        .addComponent(jLabel77)
                        .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                        .addComponent(staffIDLabel)))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(jPanel2Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel65)
                    .addComponent(jLabel66))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(jPanel2Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(lastNameLabel)
                    .addComponent(firstNameLabel))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addComponent(jLabel67)
                .addGap(7, 7, 7)
                .addComponent(otherNamesLabel)
                .addContainerGap())
        );

        jPanel3.setOpaque(false);

        fingerPrintLabel.setBackground(new java.awt.Color(255, 255, 255));
        fingerPrintLabel.setIcon(new javax.swing.ImageIcon(getClass().getResource("/resources/biometrics.png"))); // NOI18N
        fingerPrintLabel.setOpaque(true);

        fingerprintButton.setText("Read Fingerprint");
        fingerprintButton.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                fingerprintButtonActionPerformed(evt);
            }
        });

        javax.swing.GroupLayout jPanel3Layout = new javax.swing.GroupLayout(jPanel3);
        jPanel3.setLayout(jPanel3Layout);
        jPanel3Layout.setHorizontalGroup(
            jPanel3Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(jPanel3Layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(jPanel3Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                    .addComponent(fingerprintButton, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                    .addGroup(jPanel3Layout.createSequentialGroup()
                        .addComponent(fingerPrintLabel, javax.swing.GroupLayout.PREFERRED_SIZE, 168, javax.swing.GroupLayout.PREFERRED_SIZE)
                        .addGap(0, 0, Short.MAX_VALUE)))
                .addContainerGap())
        );
        jPanel3Layout.setVerticalGroup(
            jPanel3Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(jPanel3Layout.createSequentialGroup()
                .addContainerGap()
                .addComponent(fingerPrintLabel, javax.swing.GroupLayout.PREFERRED_SIZE, 175, javax.swing.GroupLayout.PREFERRED_SIZE)
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addComponent(fingerprintButton)
                .addContainerGap())
        );

        loadLabel.setHorizontalAlignment(javax.swing.SwingConstants.CENTER);
        loadLabel.setIcon(new javax.swing.ImageIcon(getClass().getResource("/resources/loader.gif"))); // NOI18N
        loadLabel.setText("Waiting for response...");
        loadLabel.setIconTextGap(10);

        connectButton.setFont(new java.awt.Font("Segoe UI Semilight", 0, 18)); // NOI18N
        connectButton.setIcon(new javax.swing.ImageIcon(getClass().getResource("/resources/web_20.png"))); // NOI18N
        connectButton.setText("Connect");
        connectButton.setIconTextGap(10);
        connectButton.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                connectButtonActionPerformed(evt);
            }
        });

        javax.swing.GroupLayout jPanel1Layout = new javax.swing.GroupLayout(jPanel1);
        jPanel1.setLayout(jPanel1Layout);
        jPanel1Layout.setHorizontalGroup(
            jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(jPanel1Layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING, false)
                    .addComponent(jPanel2, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                    .addComponent(loadLabel, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                    .addGroup(jPanel1Layout.createSequentialGroup()
                        .addGap(0, 0, Short.MAX_VALUE)
                        .addComponent(jPanel3, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                    .addComponent(connectButton, javax.swing.GroupLayout.Alignment.TRAILING, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE))
                .addContainerGap())
        );
        jPanel1Layout.setVerticalGroup(
            jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(jPanel1Layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                    .addComponent(jPanel3, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(jPanel2, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING, false)
                    .addComponent(loadLabel, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                    .addComponent(connectButton, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE))
                .addContainerGap())
        );

        javax.swing.GroupLayout layout = new javax.swing.GroupLayout(getContentPane());
        getContentPane().setLayout(layout);
        layout.setHorizontalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addComponent(jPanel1, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
        );
        layout.setVerticalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addComponent(jPanel1, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                .addGap(0, 0, 0))
        );

        pack();
    }// </editor-fold>//GEN-END:initComponents

    private void fingerprintButtonActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_fingerprintButtonActionPerformed
        // TODO add your handling code here:
        readFingerPrint();
    }//GEN-LAST:event_fingerprintButtonActionPerformed

    private void connectButtonActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_connectButtonActionPerformed
        // TODO add your handling code here:
        connect();
    }//GEN-LAST:event_connectButtonActionPerformed

    // Variables declaration - do not modify//GEN-BEGIN:variables
    private javax.swing.JButton connectButton;
    private javax.swing.JLabel fingerPrintLabel;
    private javax.swing.JButton fingerprintButton;
    private javax.swing.JLabel firstNameLabel;
    private javax.swing.JLabel jLabel65;
    private javax.swing.JLabel jLabel66;
    private javax.swing.JLabel jLabel67;
    private javax.swing.JLabel jLabel77;
    private javax.swing.JPanel jPanel1;
    private javax.swing.JPanel jPanel2;
    private javax.swing.JPanel jPanel3;
    private javax.swing.JLabel lastNameLabel;
    private javax.swing.JLabel loadLabel;
    private javax.swing.JLabel otherNamesLabel;
    private javax.swing.JLabel picLabel;
    private javax.swing.JLabel staffIDLabel;
    // End of variables declaration//GEN-END:variables
}
