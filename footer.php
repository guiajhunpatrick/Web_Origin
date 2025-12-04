<?php
if (basename($_SERVER['PHP_SELF']) == 'footer.php') {
    header("Location: index.php");
    exit();
}
?>

<div class="auth-modal" id="authModal">
    <div class="modal-content">
        <button class="close-modal" id="closeModal">&times;</button>
        <form id="loginForm" class="form active">
            <h2 style="color: #33867d;">Log In</h2>
            <div class="form-group">
                <input type="email" id="loginEmail" required placeholder=" ">
                <label>Email</label>
                <span class="error-message"></span>
            </div>
            <div class="form-group">
                <input type="password" id="loginPassword" required placeholder=" ">
                <label>Password</label>
                <i class="fas fa-eye-slash toggle-password"></i>
                <span class="error-message"></span>
            </div>
            <button type="submit" style="background-color: #33867d;">Log In</button>
            <div class="form-links">
                <a id="showSignup">No account yet?</a>
                <a id="showForgot">Forgot Password</a>
            </div>
        </form>
        <form id="signupForm" class="form">
            <h2 style="color: #33867d;">Sign Up</h2>
            <div class="form-group">
                <input type="text" id="signupName" required placeholder=" ">
                <label>Full Name</label>
                <span class="error-message"></span>
            </div>
            <div class="form-group">
                <input type="email" id="signupEmail" required placeholder=" ">
                <label>Email</label>
                <span class="error-message"></span>
            </div>
            <div class="form-group">
                <textarea id="signupAddress" required placeholder=" "></textarea>
                <label>Address</label>
                <span class="error-message"></span>
            </div>
            <div class="form-group">
                <input type="tel" id="signupPhone" required placeholder=" ">
                <label>Contact Number</label>
                <span class="error-message"></span>
            </div>
            <div class="form-group">
                <input type="password" id="signupPassword" required placeholder=" ">
                <label>Password</label>
                <i class="fas fa-eye-slash toggle-password"></i>
                <span class="error-message"></span>
            </div>
            <div class="form-group">
                <input type="password" id="signupRetypePassword" required placeholder=" ">
                <label>Retype Password</label>
                <i class="fas fa-eye-slash toggle-password"></i>
                <span class="error-message"></span>
            </div>
            <button type="submit" style="background-color: #33867d;">Submit</button>
            <div class="form-links">
                <a id="showLogin">Already have an account? Log In</a>
            </div>
        </form>
        <form id="forgotForm" class="form">
            <h2 style="color: #33867d;">Reset Password</h2>
            <div class="form-group">
                <input type="email" id="forgotEmail" required placeholder=" ">
                <label>Email</label>
                <span class="error-message"></span>
            </div>
            <button type="submit" style="background-color: #33867d;">Reset Password</button>
            <div class="form-links">
                <a id="backToLogin">Back to Log In</a>
            </div>
        </form>
    </div>
</div>

<div class="cart-modal" id="cartModal">
    <div class="cart-modal-content">
        <div class="cart-header">
            <h2>Shopping Cart</h2>
            <button class="close-cart" id="closeCart">&times;</button>
        </div>
        <div class="cart-items" id="cartItems">
            <p class="empty-cart">Your cart is empty</p>
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                <span>Total:</span>
                <span id="cartTotal">₱0.00</span>
            </div>
            <button class="checkout-btn" id="checkoutBtn" style="background: linear-gradient(to right, #e96a57, #f08a5d); border: none;">Checkout</button>
        </div>
    </div>
</div>

<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-col">
            <h3>ORIGIN</h3>
            <p style="line-height: 1.6; color: #bbb;">
                Discover your perfect style. <br>
                Authentic designs for the modern individual.
            </p>
            <div class="social-links" style="margin-top: 20px;">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
        </div>

        <div class="footer-col">
            <h4>Shop</h4>
            <ul>
                <li><a href="products.php">All Products</a></li>
                <li><a href="products.php">New Arrivals</a></li>
                <li><a href="products.php">Accessories</a></li>
                <li><a href="products.php">Sale</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Support</h4>
            <ul>
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href="contact.php">Returns</a></li>
                <li><a href="contact.php">FAQs</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Get in Touch</h4>
            <ul style="color: #bbb;">
                <li><i class="fas fa-map-marker-alt" style="margin-right:10px; color: #33867d;"></i>123 Fashion Street, Metro Manila, Philippines</li>
                <li><i class="fas fa-phone" style="margin-right:10px; color: #33867d;"></i>+63 923 456 7890</li>
                <li><i class="fas fa-envelope" style="margin-right:10px; color: #33867d;"></i> Originofficial@gmail.com</li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; 2025 Origin Fashion. All rights reserved.</p>
    </div>
</footer>

</body>
