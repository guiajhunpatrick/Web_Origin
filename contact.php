<?php
$page_title = 'Contact Us - Origin Fashion Store';
include 'header.php';
?>

<section id="contact" class="page-section active">
    <div class="content-wrapper">
        <h1 style="text-align: center; margin-bottom: 3rem;">Contact Us</h1>
        <div class="contact-container">
            <div class="contact-info">
                <h2>Get in Touch</h2>
                <p>Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>

                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h3>Address</h3>
                        <p>123 Fashion Street, Metro Manila, Philippines</p>
                    </div>
                </div>

                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h3>Phone</h3>
                        <p>+63 923 456 7890</p>
                    </div>
                </div>

                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h3>Email</h3>
                        <p>Originofficial@gmail.com</p>
                    </div>
                </div>

                <div class="contact-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h3>Business Hours</h3>
                        <p>Mon - Fri: 9:00 AM - 6:00 PM<br>Sat: 10:00 AM - 4:00 PM</p>
                    </div>
                </div>
            </div>

            <form class="contact-form" id="contactForm">
                <div class="form-group">
                    <input type="text" id="contactName" required placeholder=" ">
                    <label>Your Name</label>
                </div>

                <div class="form-group">
                    <input type="email" id="contactEmail" required placeholder=" ">
                    <label>Your Email</label>
                </div>

                <div class="form-group">
                    <input type="text" id="contactSubject" required placeholder=" ">
                    <label>Subject</label>
                </div>

                <div class="form-group">
                    <textarea id="contactMessage" required placeholder=" "></textarea>
                    <label>Message</label>
                </div>

                <button type="submit">Send Message</button>
            </form>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>