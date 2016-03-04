
import java.io.IOException;
import java.util.logging.Level;
import java.util.logging.Logger;

/**
 *
 * @author Victor Anuebunwa
 */
public class TestClass {

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        WebConnection c = WebConnection.getInstance();
        try {
            c.queryServer("s-0001");
            System.out.println(c.getStatus());
        } catch (IllegalArgumentException | IOException ex) {
            Logger.getLogger(TestClass.class.getName()).log(Level.SEVERE, null, ex);
        }
    }

}
