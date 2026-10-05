MY DHIVA TAMIL STATIONERY HUB — CUSTOMER SITE
================================================

WHAT THIS IS
------------
This is the customer-facing storefront that pairs with your existing
seller/admin site. It uses the SAME MySQL database (dhiva_stationery)
and the SAME `products` and `users` tables — so any product you add
from the admin dashboard shows up here automatically, and customers
who register here can also be viewed from the seller's user list.

It adds two new tables automatically the first time any page runs:
  - orders        (one row per placed order)
  - order_items   (line items for each order)

SETUP (XAMPP / Apache)
-----------------------
1. Copy this whole "customer-site" folder into your htdocs directory,
   e.g.  C:\xampp\htdocs\customer-site

2. Make sure XAMPP's Apache and MySQL are both running, and that the
   dhiva_stationery database (from your seller site) already exists.

3. Open your browser and go to:
       http://localhost/customer-site/

4. That's it — browsing, cart, checkout, login/register and order
   history all work against your existing database.

PAGES INCLUDED
--------------
  index.php          Home page / product catalog (search + category filter)
  product.php         Single product detail page with "Add to Cart"
  cart.php            View / update / remove cart items
  add_to_cart.php      Handler that adds an item to the session cart
  checkout.php         Delivery details form → places the order
  order_success.php    Order confirmation page
  orders.php           "My Orders" order history (requires login)
  login.php / register.php / logout.php   Customer accounts

NOTES
-----
- The cart is stored in the PHP session, so it's per-browser and
  clears if the session ends or you restart Apache with sessions
  cleared. This is normal for a lightweight demo.
- Checkout requires the customer to be logged in; if they aren't,
  they're sent to login.php and returned to checkout afterward.
- Placing an order automatically reduces the product's stock
  quantity in the `products` table, so your seller dashboard will
  reflect it immediately.
- Payment is Cash on Delivery only (no real payment gateway) —
  this keeps the demo simple and focused on the PHP + MySQL flow.
