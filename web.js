$(document).ready(function () {
    let cart = [];
    let isLoggedIn = false;
    let currentUser = null;

    checkLoginStatus();

    function checkLoginStatus() {
        $.ajax({
            url: 'auth.php',
            method: 'POST',
            data: { action: 'check_session' },
            dataType: 'json',
            success: function (response) {
                if (response.logged_in) {

                    isLoggedIn = true;
                    currentUser = response.user_name;
                    $('#loginButton span').text(response.user_name);

                    loadCartFromDatabase();

                } else {
                    isLoggedIn = false;
                    currentUser = null;
                    $('#loginButton span').text('Log In');
                    cart = [];
                    updateCartCount();
                }
            },
            error: function () {
                console.log('Could not check login status');
                isLoggedIn = false;
                cart = [];
                updateCartCount();
            }
        });
    }

    function loadCartFromDatabase() {
        $.ajax({
            url: 'add_to_cart.php',
            method: 'POST',
            data: { action: 'get' },
            dataType: 'json',
            success: function (response) {
                if (response.success && response.cart) {
                    cart = response.cart.map(item => ({
                        id: item.id,
                        product_name: item.product_name,
                        product_price: parseFloat(item.product_price),
                        quantity: parseInt(item.quantity)
                    }));
                    updateCartCount();
                    updateCartDisplay();
                }
            },
            error: function () {
                console.log('Could not load cart from database');
            }
        });
    }


    $(document).on('click', '.add-to-cart', function () {
        if (!isLoggedIn) {
            alert('Please log in to add items to your cart.');
            $('#authModal').addClass('active');
            $('body').addClass('modal-open');
            return;
        }

        const productCard = $(this).closest('.product-card');
        const productName = productCard.data('product-name');
        const productPrice = parseFloat(productCard.data('product-price'));
        const btn = $(this);

        btn.prop('disabled', true);

        $.ajax({
            url: 'add_to_cart.php',
            method: 'POST',
            data: {
                action: 'add',
                product_name: productName,
                product_price: productPrice
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    btn.text('Added!').css('background', '#27ae60');
                    setTimeout(() => {
                        btn.text('Add to Cart').css('background', '');
                    }, 1000);

                    loadCartFromDatabase();

                } else {
                    alert(response.message || 'Could not add item to cart');
                }
            },
            error: function (xhr, status, error) {
                console.error('Add to cart error:', error);
                alert('Failed to add item to cart. Please try again.');
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    });

    function updateCartCount() {
        const totalItems = cart.reduce((sum, item) => sum + parseInt(item.quantity), 0);
        $('.cart-count').text(totalItems);
    }

    function updateCartDisplay() {
        const cartItemsContainer = $('#cartItems');
        cartItemsContainer.empty();

        if (!isLoggedIn) {
            cartItemsContainer.html('<p class="empty-cart">Please log in to view your cart</p>');
            $('#cartTotal').text('₱0.00');
            $('#checkoutBtn').prop('disabled', true);
            return;
        }

        if (cart.length === 0) {
            cartItemsContainer.html('<p class="empty-cart">Your cart is empty</p>');
            $('#cartTotal').text('₱0.00');
            $('#checkoutBtn').prop('disabled', true);
            return;
        }

        $('#checkoutBtn').prop('disabled', false);
        let total = 0;

        cart.forEach((item) => {
            const price = parseFloat(item.product_price);
            const qty = parseInt(item.quantity);
            const itemTotal = price * qty;
            total += itemTotal;

            const itemId = item.id;

            const cartItemHTML = `
                <div class="cart-item">
                    <div class="cart-item-info">
                        <h4>${item.product_name}</h4>
                        <p class="cart-item-price">₱${price.toFixed(2)} each</p>
                    </div>
                    <div class="cart-item-actions">
                        <div class="quantity-controls">
                            <button class="quantity-btn decrease-qty" data-id="${itemId}" data-qty="${qty}">-</button>
                            <span class="quantity">${qty}</span>
                            <button class="quantity-btn increase-qty" data-id="${itemId}" data-qty="${qty}">+</button>
                        </div>
                        <button class="remove-item" data-id="${itemId}">Remove</button>
                    </div>
                </div>
            `;
            cartItemsContainer.append(cartItemHTML);
        });

        $('#cartTotal').text('₱' + total.toFixed(2));
    }

    $(document).on('click', '.increase-qty', function () {
        if (!isLoggedIn) return;

        const itemId = $(this).data('id');
        const currentQty = parseInt($(this).data('qty'));
        const newQty = currentQty + 1;

        $.ajax({
            url: 'add_to_cart.php',
            method: 'POST',
            data: {
                action: 'update_quantity',
                cart_id: itemId,
                quantity: newQty
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    loadCartFromDatabase();
                }
            }
        });
    });

    $(document).on('click', '.decrease-qty', function () {
        if (!isLoggedIn) return;

        const itemId = $(this).data('id');
        const currentQty = parseInt($(this).data('qty'));

        if (currentQty > 1) {
            const newQty = currentQty - 1;

            $.ajax({
                url: 'add_to_cart.php',
                method: 'POST',
                data: {
                    action: 'update_quantity',
                    cart_id: itemId,
                    quantity: newQty
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        loadCartFromDatabase();
                    }
                }
            });
        }
    });

    $(document).on('click', '.remove-item', function () {
        if (!isLoggedIn) return;

        const itemId = $(this).data('id');

        if (confirm('Remove this item from cart?')) {
            $.ajax({
                url: 'add_to_cart.php',
                method: 'POST',
                data: { action: 'remove', cart_id: itemId },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        loadCartFromDatabase();
                    }
                }
            });
        }
    });

    $('#checkoutBtn').click(function () {
        if (!isLoggedIn) {
            alert('Please log in to complete checkout.');
            $('#cartModal').removeClass('active');
            $('#authModal').addClass('active');
            $('body').addClass('modal-open');
            return;
        }

        if (cart.length > 0) {
            $(this).prop('disabled', true).text('Processing...');

            $.ajax({
                url: 'add_to_cart.php',
                method: 'POST',
                data: { action: 'checkout' },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Order placed successfully!\nTotal: ₱' + response.total);
                        cart = [];
                        updateCartCount();
                        updateCartDisplay();
                        $('#cartModal').removeClass('active');
                        $('body').removeClass('modal-open');
                    } else {
                        alert(response.message);
                    }
                },
                error: function () {
                    alert('Checkout failed. Please try again.');
                },
                complete: function () {
                    $('#checkoutBtn').prop('disabled', false).text('Checkout');
                }
            });
        }
    });

    $('.site-title').click(function () {
        window.location.href = 'index.php';
    });

    $('#shopNowBtn').click(function () {
        window.location.href = 'products.php';
    });

    $('#loginButton').click(function (e) {
        e.preventDefault();
        if (isLoggedIn) {
            if (confirm('Do you want to log out?')) {
                logout();
            }
        } else {
            $('#authModal').addClass('active');
            $('body').addClass('modal-open');
        }
    });

    $('#cartIcon').click(function (e) {
        e.preventDefault();

        if (!isLoggedIn) {
            alert('Please log in to view your cart.');
            $('#authModal').addClass('active');
            $('body').addClass('modal-open');
            return;
        }

        $('#cartModal').addClass('active');
        $('body').addClass('modal-open');

        loadCartFromDatabase();
    });

    $('#closeCart').click(function () {
        $('#cartModal').removeClass('active');
        $('body').removeClass('modal-open');
    });

    $('#cartModal').click(function (e) {
        if (e.target.id === 'cartModal') {
            $('#cartModal').removeClass('active');
            $('body').removeClass('modal-open');
        }
    });

    function logout() {
        $.ajax({
            url: 'auth.php',
            method: 'POST',
            data: { action: 'logout' },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    isLoggedIn = false;
                    currentUser = null;
                    cart = [];
                    updateCartCount();
                    updateCartDisplay();
                    $('#loginButton span').text('Log In');
                    alert('You have been logged out successfully.');
                    location.reload();
                }
            },
            error: function () {
                alert('Logout failed. Please try again.');
            }
        });
    }

    $('#closeModal').click(function () {
        $('#authModal').removeClass('active');
        $('body').removeClass('modal-open');
    });

    $('#authModal').click(function (e) {
        if (e.target.id === 'authModal') {
            $('#authModal').removeClass('active');
            $('body').removeClass('modal-open');
        }
    });

    function switchForm(targetForm) {
        $('.form.active').fadeOut(200, function () {
            $(this).removeClass('active');
            $(targetForm).fadeIn(200).addClass('active');
        });
    }

    $('#showSignup').click(function (e) {
        e.preventDefault();
        switchForm('#signupForm');
    });

    $('#showLogin, #backToLogin').click(function (e) {
        e.preventDefault();
        switchForm('#loginForm');
    });

    $('#showForgot').click(function (e) {
        e.preventDefault();
        switchForm('#forgotForm');
    });

    $('.toggle-password').click(function () {
        $(this).toggleClass('fa-eye fa-eye-slash');
        let input = $(this).siblings('input');
        input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
    });

    function showError(inputElement, message) {
        inputElement.addClass('invalid');
        inputElement.siblings('.error-message').text(message);
    }

    function clearError(inputElement) {
        inputElement.removeClass('invalid');
        inputElement.siblings('.error-message').text('');
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function validatePhone(phone) {
        const re = /^\+?[\d\s-]{10,}$/;
        return re.test(phone);
    }

    $('.form-group input, .form-group textarea').on('input', function () {
        clearError($(this));
    });

    $('#loginForm').submit(function (e) {
        e.preventDefault();
        let isValid = true;
        const emailInput = $('#loginEmail');
        const passwordInput = $('#loginPassword');

        clearError(emailInput);
        clearError(passwordInput);

        if (!validateEmail(emailInput.val())) {
            showError(emailInput, 'Please enter a valid email.');
            isValid = false;
        }
        if (passwordInput.val().length < 6) {
            showError(passwordInput, 'Password must be at least 6 characters.');
            isValid = false;
        }

        if (isValid) {
            $.ajax({
                url: 'auth.php',
                method: 'POST',
                data: {
                    action: 'login',
                    email: emailInput.val(),
                    password: passwordInput.val()
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        isLoggedIn = true;
                        currentUser = response.user_name;
                        alert('Login successful!');
                        $('#authModal').removeClass('active');
                        $('body').removeClass('modal-open');
                        $('#loginButton span').text(response.user_name);
                        $('#loginForm')[0].reset();

                        loadCartFromDatabase();
                    } else {
                        alert(response.message);
                    }
                },
                error: function () {
                    alert('Login failed. Please try again.');
                }
            });
        }
    });

    $('#signupForm').submit(function (e) {
        e.preventDefault();
        let isValid = true;
        const nameInput = $('#signupName');
        const emailInput = $('#signupEmail');
        const addressInput = $('#signupAddress');
        const phoneInput = $('#signupPhone');
        const passwordInput = $('#signupPassword');
        const retypePasswordInput = $('#signupRetypePassword');

        $('#signupForm .form-group input, #signupForm .form-group textarea').each(function () {
            clearError($(this));
        });

        if (nameInput.val().trim().length < 2) {
            showError(nameInput, 'Please enter your full name.');
            isValid = false;
        }
        if (!validateEmail(emailInput.val())) {
            showError(emailInput, 'Please enter a valid email.');
            isValid = false;
        }
        if (addressInput.val().trim().length < 10) {
            showError(addressInput, 'Please enter a complete address.');
            isValid = false;
        }
        if (!validatePhone(phoneInput.val()) || phoneInput.val().length < 11) {
            showError(phoneInput, 'Please enter a valid phone number (at least 11 digits).');
            isValid = false;
        }
        if (passwordInput.val().trim().length < 6) {
            showError(passwordInput, 'Password must be at least 6 characters.');
            isValid = false;
        }
        if (passwordInput.val() !== retypePasswordInput.val()) {
            showError(retypePasswordInput, 'Passwords do not match.');
            isValid = false;
        }

        if (isValid) {
            $.ajax({
                url: 'auth.php',
                method: 'POST',
                data: {
                    action: 'signup',
                    name: nameInput.val(),
                    email: emailInput.val(),
                    address: addressInput.val(),
                    phone: phoneInput.val(),
                    password: passwordInput.val()
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Registration successful! Please log in.');
                        switchForm('#loginForm');
                        $('#signupForm')[0].reset();
                    } else {
                        alert(response.message);
                    }
                },
                error: function () {
                    alert('Registration failed. Please try again.');
                }
            });
        }
    });

    $('#forgotForm').submit(function (e) {
        e.preventDefault();
        let isValid = true;
        const emailInput = $('#forgotEmail');

        clearError(emailInput);

        if (!validateEmail(emailInput.val())) {
            showError(emailInput, 'Please enter a valid email.');
            isValid = false;
        }

        if (isValid) {
            alert('Password reset instructions have been sent to your email.');
            switchForm('#loginForm');
            $('#forgotForm')[0].reset();
        }
    });

    $('#contactForm').submit(function (e) {
        e.preventDefault();
        let isValid = true;
        const nameInput = $('#contactName');
        const subjectInput = $('#contactSubject');
        const messageInput = $('#contactMessage');

        if (nameInput.val().trim().length === 0 ||
            subjectInput.val().trim().length === 0 ||
            messageInput.val().trim().length === 0) {
            alert('Please fill out all fields.');
            isValid = false;
        }

        if (isValid) {
            alert('Thank you for contacting us! We will get back to you soon.');
            $(this)[0].reset();
        }
    });
});