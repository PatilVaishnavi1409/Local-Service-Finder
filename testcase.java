try (Scanner scanner = new Scanner(System.in)) {
    System.out.print("Enter the dividend: ");
    double dividend = Double.parseDouble(scanner.nextLine().trim());

    System.out.print("Enter the divisor: ");
    double divisor = Double.parseDouble(scanner.nextLine().trim());

    if (divisor == 0) {
        System.out.println("Error: Division by zero is not allowed.");
    } else {
        double result = dividend / divisor;
        if (Double.isInfinite(result) || Double.isNaN(result)) {
            System.out.println("Error: Result is out of range.");
        } else {
            System.out.println("Result: " + result);
        }
    }
} catch (NumberFormatException e) {
    System.out.println("Error: Please enter valid numbers.");
}
