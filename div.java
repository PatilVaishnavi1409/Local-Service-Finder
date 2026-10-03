import java.util.Scanner;

public class Division {
    public static void main(String[] args) {
        Scanner scanner = new Scanner(System.in);

        System.out.print("Enter the dividend: ");
        double dividend = scanner.nextDouble();

        System.out.print("Enter the divisor: ");
        double divisor = scanner.nextDouble();

        if (divisor == 0) {
            System.out.println("Error: Division by zero is not allowed.");
        } else {
            double quotient = dividend / divisor;
            System.out.println("Result: " + quotient);
        }

        scanner.close();
    }
}
