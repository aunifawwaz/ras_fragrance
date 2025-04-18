<?php
// footer.php

// Dynamic Year
$currentYear = date("Y");
?>
<style>
    .social-media {
    flex: 1;
    text-align: center;
}

.social-media .social-icon {
    margin: 0 10px;
}

.social-media img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    transition: transform 0.3s ease;
}

.social-media img:hover {
    transform: scale(1.1);
}

</style>
<footer>
    <div class="footer-content">
        <!-- Address Section -->
        <div class="address">
            <h4>Our Address</h4>
            <p>No.9, 
            Jalan Bahagia, Rezab 3, 
            Kampung Melayu Raya, 
            81500 Pekan Nanas, 
            Johor, 
            Malaysia</p>
        </div>
        
        <!-- Email Subscription Form -->
        <div class="subscribe">
            <h4>For any enquiries can search us on:</h4>
        </div>

        <!-- Social Media Links -->
        <div class="social-media">
            <a href="https://www.instagram.com/youraccount" target="_blank" class="social-icon">
                <img src="images/instagram.png" alt="Instagram">
            </a>
            <a href="https://wa.me/yourwhatsappnumber" target="_blank" class="social-icon">
                <img src="images/whatsapp.png" alt="WhatsApp">
            </a>
            <a href="https://www.tiktok.com/@youraccount" target="_blank" class="social-icon">
                <img src="images/tiktok.png" alt="TikTok">
            </a>
        </div>
    </div>

    <p>&copy; <?php echo $currentYear; ?> Ras Fragrance. All rights reserved.</p>
</footer>
