Unsecure Online Bookstore

Admin Features:
- Secure Login (vulnerable)
- Dashboard
- Manage Categories, Subcategories, Books
- Manage Inventory, Orders
- View Sales Report
- Manage Account

Client Features:
- Secure Login and Registration (vulnerable)
- Explore Books, Search, View Details
- Add to Cart, Checkout, Pay Online
- Manage/View Orders

Vulnerabilities Included:
- SQL Injection (Login, Explore, Book Detail)
- Cross Site Scripting (XSS) (Book Titles, Cart)
- Cross Site Request Forgery (CSRF) (Cart, Payment)
- Insecure Direct Object Reference (IDOR) (Orders, Payments)
- Broken Authentication (Login, Payment)
- Payment Tampering (payment.php)
- Sensitive Data Exposure (Sales Report)
- No Session Management
- No Output Sanitization
- No Password Hashing (Plaintext passwords)
- No CSRF Tokens
- Direct SQL Queries without Prepared Statements
