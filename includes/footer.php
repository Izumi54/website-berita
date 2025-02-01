        <!-- Footer -->
        <footer class="bg-dark text-white mt-5">
            <div class="container py-4">
                <div class="row">
                    <div class="col-md-4">
                        <h5>Tentang Kami</h5>
                        <p>Portal berita terpercaya yang menyajikan informasi terkini dan akurat seputar berita nasional, politik, ekonomi, olahraga, teknologi dan berbagai topik menarik lainnya.</p>
                    </div>
                    <div class="col-md-4">
                        <h5>Kategori</h5>
                        <ul class="list-unstyled">
                            <?php
                            $footer_categories = $conn->query("SELECT * FROM categories ORDER BY name LIMIT 6");
                            while($cat = $footer_categories->fetch_assoc()):
                            ?>
                            <li>
                                <a href="kategori.php?slug=<?php echo $cat['slug']; ?>" class="text-white">
                                    <?php echo $cat['name']; ?>
                                </a>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h5>Hubungi Kami</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-envelope mr-2"></i> info@portalberita.com</li>
                            <li><i class="fas fa-phone mr-2"></i> (021) 1234567</li>
                            <li><i class="fas fa-map-marker-alt mr-2"></i> Jl. Contoh No. 123, Jakarta</li>
                        </ul>
                        <div class="social-media mt-3">
                            <a href="#" class="text-white mr-3"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-white mr-3"><i class="fab fa-twitter"></i></a>
                            <a href="https://www.instagram.com/lensanewsroom?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" class="text-white mr-3"><i class="fab fa-instagram"></i></a>
                            <a href="https://youtube.com/@lpmlensamedia?si=bw9GsagA8rxYUAsi" class="text-white"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center py-3 border-top border-secondary">
                <small>&copy; <?php echo date('Y'); ?> Portal Berita. All rights reserved.</small>
            </div>
        </footer>

        <!-- Back to Top Button -->
        <button class="btn btn-primary back-to-top" onclick="scrollToTop()">
            <i class="fas fa-arrow-up"></i>
        </button>

        <!-- JavaScript -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        
        <script>
            // Show/hide back to top button
            $(window).scroll(function() {
                if ($(this).scrollTop() > 200) {
                    $('.back-to-top').fadeIn();
                } else {
                    $('.back-to-top').fadeOut();
                }
            });

            // Smooth scroll to top
            function scrollToTop() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            // Loading spinner
            $(window).on('load', function() {
                $('.loading').fadeOut();
            });

            // Lazy loading images
            document.addEventListener("DOMContentLoaded", function() {
                var lazyImages = [].slice.call(document.querySelectorAll("img.lazy"));

                if ("IntersectionObserver" in window) {
                    let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                        entries.forEach(function(entry) {
                            if (entry.isIntersecting) {
                                let lazyImage = entry.target;
                                lazyImage.src = lazyImage.dataset.src;
                                lazyImage.classList.remove("lazy");
                                lazyImageObserver.unobserve(lazyImage);
                            }
                        });
                    });

                    lazyImages.forEach(function(lazyImage) {
                        lazyImageObserver.observe(lazyImage);
                    });
                }
            });
        </script>

        <!-- Newsletter Section with Improved Validation -->
        <div class="newsletter-section py-5 bg-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-6 text-center">
                        <h4>Dapatkan Berita Terbaru</h4>
                        <p>Berlangganan newsletter kami untuk mendapatkan update berita terkini</p>
                        
                        <!-- Newsletter Form with AJAX -->
                        <form id="newsletterForm" class="form-inline justify-content-center">
                            <div class="input-group w-75">
                                <input type="email" 
                                       class="form-control" 
                                       id="emailSubscribe" 
                                       placeholder="Masukkan email Anda" 
                                       required 
                                       pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-danger">
                                        <span class="normal-text">Subscribe</span>
                                        <span class="loading-text d-none">
                                            <i class="fas fa-spinner fa-spin"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Feedback Messages -->
                        <div class="mt-3">
                            <div id="subscribeSuccess" class="alert alert-success d-none">
                                Terima kasih! Email Anda telah berhasil didaftarkan.
                            </div>
                            <div id="subscribeError" class="alert alert-danger d-none">
                                Maaf, terjadi kesalahan. Silakan coba lagi nanti.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Newsletter JavaScript -->
        <script>
        document.getElementById('newsletterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get elements
            const form = this;
            const email = document.getElementById('emailSubscribe').value;
            const successMsg = document.getElementById('subscribeSuccess');
            const errorMsg = document.getElementById('subscribeError');
            const submitBtn = form.querySelector('button[type="submit"]');
            const normalText = submitBtn.querySelector('.normal-text');
            const loadingText = submitBtn.querySelector('.loading-text');
            
            // Show loading state
            normalText.classList.add('d-none');
            loadingText.classList.remove('d-none');
            submitBtn.disabled = true;
            
            // Hide previous messages
            successMsg.classList.add('d-none');
            errorMsg.classList.add('d-none');
            
            // Send AJAX request
            fetch('subscribe.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    successMsg.classList.remove('d-none');
                    form.reset();
                } else {
                    errorMsg.textContent = data.message || 'Terjadi kesalahan. Silakan coba lagi.';
                    errorMsg.classList.remove('d-none');
                }
            })
            .catch(error => {
                errorMsg.textContent = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
                errorMsg.classList.remove('d-none');
            })
            .finally(() => {
                // Reset button state
                normalText.classList.remove('d-none');
                loadingText.classList.add('d-none');
                submitBtn.disabled = false;
            });
        });
        </script>
    </body>
</html> 