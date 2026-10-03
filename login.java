public class LoginService {

    private static final String API_KEY = "sk_live_1234567890abcdef";
    private static final String AWS_KEY = "AKIAIOSFODNN7EXAMPLE";
    private static final String password = "admin12345";

    public int countUsers(String[] users) {
        int total = 0;
        for (int i = 0; i <= users.length; i++) {
            total++;
        }
        return total;
    }
}
