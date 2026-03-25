<?php
session_start();

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filipino Homes — Investment Properties & Services</title>
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,700;1,400;1,500&family=Jost:wght@300;400;500;600&display=swap"
        rel="stylesheet">
</head>

<body>

    <header id="hdr">
        <a href="#" class="logo">
            <img src="assets/images/logo.png" alt="Filipino Homes Logo" class="logo-icon">
            <span>
                <span style="display:block;line-height:1.1;">Filipino Homes</span>
                <span
                    style="display:block;font-family:'Jost',sans-serif;font-size:0.45rem;font-weight:500;letter-spacing:0.12em;color:var(--text-soft);text-transform:uppercase;">Investment
                    Properties & Services</span>
            </span>
        </a>

        <nav>
            <a href="#hero">Home</a>
            <a href="#about">About</a>
            <a href="#rooms">Rooms</a>
            <a href="#reviews">Reviews</a>
            <a href="#contact">Contact</a>
        </nav>

        <div style="display:flex;align-items:center;gap:0.75rem;">
            <button class="btn-login-header">Log In</button>
            <button class="btn-book-header"
                onclick="document.querySelector('#cta').scrollIntoView({behavior:'smooth'})">Book Now</button>
            <button class="hamburger" id="hamburger" aria-label="Menu"><span></span><span></span><span></span></button>
        </div>
    </header>

    <div class="mobile-nav" id="mobileNav">
        <a href="#hero" onclick="closeMob()">Home</a>
        <a href="#about" onclick="closeMob()">About</a>
        <a href="#rooms" onclick="closeMob()">Rooms</a>
        <a href="#reviews" onclick="closeMob()">Reviews</a>
        <a href="#contact" onclick="closeMob()">Contact</a>
        <a href="#cta" onclick="closeMob()" style="color:var(--gold);font-weight:600;">Book Now</a>
        <button class="btn-login-header">Log In</button>
    </div>

    <div class="modal-overlay" id="loginModal">
        <div class="modal-box">
            <button class="modal-close" id="modalClose">&times;</button>
            <div id="modalAlert" style="display:none;"></div>
            
            <form id="loginForm">
                <div class="modal-form" id="tab-login">
                    <div class="modal-header">
                        <h2 class="modal-title">Welcome Back</h2>
                        <p class="modal-sub">Log in to continue your booking</p>
                    </div>

                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="modal-field">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="Email Address" required>
                    </div>

                    <div class="modal-field">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>

                    <button class="modal-btn-primary" type="submit">Log In</button>

                    <p class="modal-switch">
                        Don't have an account?
                        <a href="#" onclick="switchTab('signup')">Sign Up</a>
                    </p>
                </div>
            </form>

            <form id="registerForm">
                <div class="modal-form" id="tab-signup">
                    <div class="modal-header">
                        <h2 class="modal-title">Create Account</h2>
                        <p class="modal-sub">Sign up to book your stay</p>
                    </div>
                    <div class="modal-field">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    </div>

                    <div class="field-name-row">
                        <div class="modal-field">
                            <label>First Name</label>
                            <input type="text" name="first_name" placeholder="First Name" required>
                        </div>
                        <div class="modal-field">
                            <label>Last Name</label>
                            <input type="text" name="last_name" placeholder="Last Name" required>
                        </div>
                    </div>

                    <div class="modal-field">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="Email Address" required>
                    </div>

                    <div class="field-phone-row">
                        <div class="modal-field">
                            <label>Country Code</label>
                            <select name="country_code" required>
                                <option value="">🌐</option>
                                <option value="+1">🇺🇸</option>
                                <option value="+63">🇵🇭</option>
                                <option value="+44">🇬🇧</option>
                                <option value="+91">🇮🇳</option>
                                <!-- Add more countries as needed -->
                            </select>
                        </div>
                        <div class="modal-field">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" placeholder="Phone Number" required>
                        </div>
                    </div>

                    <div class="modal-field">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="••••••••" required minlength="6">
                    </div>

                    <div class="modal-field">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" placeholder="••••••••" required>
                    </div>

                    <button class="modal-btn-primary" type="submit" name="register">
                        Create Account
                    </button>

                    <p class="modal-switch">
                        Already have an account?
                        <a href="#" onclick="switchTab('login')">Log In</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <section class="hero" id="hero">
        <div class="hero-text">
            <div class="hero-eyebrow">
                <span class="eyebrow-line"></span>
                Welcome to Filipino Homes
            </div>
            <h1>Experience the<br>Warmth of<br><em>Filipino Hospitality</em></h1>
            <p class="hero-desc">
                Located on the vibrant shores of Boracay, Filipino Homes offers stylish apartment accommodations where
                modern comfort meets Filipino hospitality. From sunlit balconies with ocean views to thoughtfully
                furnished interiors, every space is designed for relaxation and a true sense of <em
                    style="font-style:normal;color:var(--blue-500)">home</em>.
            </p>
            <div class="hero-stats">
                <div>
                    <div class="hero-stat-num">48+</div>
                    <div class="hero-stat-lbl">Unique Rooms</div>
                </div>
                <div>
                    <div class="hero-stat-num">4.9★</div>
                    <div class="hero-stat-lbl">Guest Rating</div>
                </div>
                <div>
                    <div class="hero-stat-num">12yr</div>
                    <div class="hero-stat-lbl">of Hospitality</div>
                </div>
            </div>
        </div>

        <div class="hero-carousel">
            <div class="carousel-frame" id="carouselFrame">
                <div class="carousel-slides" id="carouselSlides">

                    <div class="carousel-slide active room-1" data-label="Deluxe Nipa Suite"
                        data-type="Garden View · 1 King Bed">
                        <img src="assets/images/unit1.jpg" alt="Unit 1">
                    </div>

                    <div class="carousel-slide room-2" data-label="Garden Breeze Room"
                        data-type="Terrace View · 1 Queen Bed">
                        <img src="assets/images/unit2.jpg" alt="Unit 2">
                    </div>

                    <div class="carousel-slide room-3" data-label="Ocean View Suite" data-type="Sea View · 1 King Bed">
                        <img src="assets/images/unit3.jpg" alt="Unit 3">
                    </div>

                    <div class="carousel-slide room-4" data-label="Family Loft Room"
                        data-type="Mountain View · 2 Queen Beds">
                        <img src="assets/images/unit4.jpg" alt="Unit 4">
                    </div>

                    <div class="carousel-slide room-5" data-label="Bahay Kubo Suite"
                        data-type="Courtyard View · Bamboo Interior">
                        <img src="assets/images/unit5.jpg" alt="Unit 5">
                    </div>

                </div>

                <div class="carousel-overlay"></div>
                <div class="carousel-label">
                    <div class="carousel-label-name" id="slideLabel">Deluxe Nipa Suite</div>
                    <div class="carousel-label-type" id="slideType">Garden View · 1 King Bed</div>
                </div>

                <div class="carousel-controls">
                    <button class="carousel-btn" id="prevBtn" aria-label="Previous">
                        <svg viewBox="0 0 24 24">
                            <polyline points="15 18 9 12 15 6" />
                        </svg>
                    </button>
                    <button class="carousel-btn" id="nextBtn" aria-label="Next">
                        <svg viewBox="0 0 24 24">
                            <polyline points="9 18 15 12 9 6" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="carousel-dots" id="carouselDots">
                <div class="dot active" data-idx="0"></div>
                <div class="dot" data-idx="1"></div>
                <div class="dot" data-idx="2"></div>
                <div class="dot" data-idx="3"></div>
                <div class="dot" data-idx="4"></div>
            </div>

            <div class="carousel-thumbs" id="carouselThumbs">
                <div class="thumb active" data-idx="0">
                    <img src="assets/images/unit1.jpg" alt="Unit 1">
                </div>
                <div class="thumb" data-idx="1">
                    <img src="assets/images/unit2.jpg" alt="Unit 2">
                </div>
                <div class="thumb" data-idx="2">
                    <img src="assets/images/unit3.jpg" alt="Unit 3">
                </div>
                <div class="thumb" data-idx="3">
                    <img src="assets/images/unit4.jpg" alt="Unit 4">
                </div>
                <div class="thumb" data-idx="4">
                    <img src="assets/images/unit5.jpg" alt="Unit 5">
                </div>
            </div>
        </div>
    </section>

    <section class="about" id="about">
        <div class="about-inner">

            <div class="about-visual reveal">
                <div class="about-img-main">
                    <img src="assets/images/owner.jpg" alt="Owner of Filipino Homes">
                </div>
                <div class="about-img-accent">
                    <img src="assets/images/hero-img.jpg" alt="Image of Filipino Homes" >
                </div>
                <div class="about-badge">
                    <div class="badge-num">12+</div>
                    <div class="badge-lbl">Years of<br>Hospitality</div>
                </div>
            </div>

            <div class="about-text reveal reveal-delay-1">
                <div class="eyebrow">Our Story</div>
                <h2 class="section-heading">A <em>Home</em> Away<br>From Home</h2>
                <p class="body-text">
                    Since 2012, the Magdaong family has welcomed guests with one simple belief: every visitor deserves
                    the warmth and care of a Filipino household. What began as a small collection of well-designed
                    apartments has grown into a cherished retreat, loved by travelers from all over the world.
                </p>
                <p class="body-text">
                    Each apartment is thoughtfully designed with a mix of contemporary finishes and local touches — from
                    capiz-inspired accents to hand-woven textiles — ensuring comfort without losing character. Our staff
                    treats every guest like family, because at Filipino Homes, <em
                        style="font-style:normal;color:var(--blue-500)">you are truly at home</em>.
                </p>
                <div class="about-pillars">
                    <div class="pillar">
                        <div class="pillar-icon"><svg viewBox="0 0 24 24">
                                <path
                                    d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
                            </svg></div>
                        <div>
                            <div class="pillar-title">Filipino Warmth</div>
                            <div class="pillar-desc">Genuine malasakit in every interaction</div>
                        </div>
                    </div>
                    <div class="pillar">
                        <div class="pillar-icon"><svg viewBox="0 0 24 24">
                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                <polyline points="9 22 9 12 15 12 15 22" />
                            </svg></div>
                        <div>
                            <div class="pillar-title">Modern Comfort</div>
                            <div class="pillar-desc">Stylish apartments with Filipino touches.</div>
                        </div>
                    </div>
                    <div class="pillar">
                        <div class="pillar-icon"><svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg></div>
                        <div>
                            <div class="pillar-title">24/7 Care</div>
                            <div class="pillar-desc">Always here whenever you need us</div>
                        </div>
                    </div>
                    <div class="pillar">
                        <div class="pillar-icon"><svg viewBox="0 0 24 24">
                                <path d="M12 22s-8-4.5-8-11.8A8 8 0 0112 2a8 8 0 018 8.2c0 7.3-8 11.8-8 11.8z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg></div>
                        <div>
                            <div class="pillar-title">Prime Location</div>
                            <div class="pillar-desc">Steps from Boracay’s beach and attractions</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rooms" id="rooms">
        <div class="rooms-header reveal">
            <div class="eyebrow" style="justify-content:center;"><span
                    style="width:24px;height:1.5px;background:var(--gold);display:block;"></span>&nbsp;Featured Rooms
            </div>
            <h2 class="section-heading">Find Your <em>Perfect</em> Room</h2>
        </div>

        <div class="rooms-grid">

            <div class="room-card reveal">
                <div class="room-card-img">
                    <div class="room-card-img-bg r-img-1">
                        <img src="assets/images/OGA 4H.jpg" alt="OGA 4H">
                    </div>
                    <span class="room-badge">Featured</span>
                </div>
                <div class="room-card-body">
                    <div class="room-name">Deluxe Nipa Suite</div>
                    <div class="room-meta">
                        <span><svg viewBox="0 0 24 24">
                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>Garden View</span>
                        <span><svg viewBox="0 0 24 24">
                                <rect x="2" y="7" width="20" height="14" rx="2" />
                                <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" />
                            </svg>1 King Bed</span>
                        <span><svg viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                            </svg>2 Guests</span>
                    </div>
                    <div class="room-divider"></div>
                    <div class="room-price-row">
                        <div class="room-price">₱3,500 <sub>/ night</sub></div>
                        <button class="btn-room">View Room</button>
                    </div>
                </div>
            </div>

            <div class="room-card reveal reveal-delay-1">
                <div class="room-card-img">
                    <div class="room-card-img-bg r-img-2">
                        <img src="assets/images/Casa Camilla unit 10.jpg" alt="Casa Camilla unit 10">
                    </div>
                    <span class="room-badge new">Garden</span>
                </div>
                <div class="room-card-body">
                    <div class="room-name">Garden Breeze Room</div>
                    <div class="room-meta">
                        <span><svg viewBox="0 0 24 24">
                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>Terrace View</span>
                        <span><svg viewBox="0 0 24 24">
                                <rect x="2" y="7" width="20" height="14" rx="2" />
                                <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" />
                            </svg>1 Queen Bed</span>
                        <span><svg viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                            </svg>2 Guests</span>
                    </div>
                    <div class="room-divider"></div>
                    <div class="room-price-row">
                        <div class="room-price">₱2,800 <sub>/ night</sub></div>
                        <button class="btn-room">View Room</button>
                    </div>
                </div>
            </div>

            <div class="room-card reveal reveal-delay-2">
                <div class="room-card-img">
                    <div class="room-card-img-bg r-img-3">
                        <img src="assets/images/RR 2nd Unit 5.jpg" alt="RR 2nd Unit 5">
                    </div>
                    <span class="room-badge popular">Sea View</span>
                </div>
                <div class="room-card-body">
                    <div class="room-name">Ocean View Suite</div>
                    <div class="room-meta">
                        <span><svg viewBox="0 0 24 24">
                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>Sea View</span>
                        <span><svg viewBox="0 0 24 24">
                                <rect x="2" y="7" width="20" height="14" rx="2" />
                                <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" />
                            </svg>1 King Bed</span>
                        <span><svg viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                            </svg>2 Guests</span>
                    </div>
                    <div class="room-divider"></div>
                    <div class="room-price-row">
                        <div class="room-price">₱4,200 <sub>/ night</sub></div>
                        <button class="btn-room">View Room</button>
                    </div>
                </div>
            </div>

            <div class="room-card reveal">
                <div class="room-card-img">
                    <div class="room-card-img-bg r-img-4">
                        <img src="assets/images/OGVA 6F.jpg" alt="OGVA 6F">
                    </div>
                    <span class="room-badge">Family</span>
                </div>
                <div class="room-card-body">
                    <div class="room-name">Family Loft Room</div>
                    <div class="room-meta">
                        <span><svg viewBox="0 0 24 24">
                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>Mountain View</span>
                        <span><svg viewBox="0 0 24 24">
                                <rect x="2" y="7" width="20" height="14" rx="2" />
                                <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" />
                            </svg>2 Queen Beds</span>
                        <span><svg viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                            </svg>4 Guests</span>
                    </div>
                    <div class="room-divider"></div>
                    <div class="room-price-row">
                        <div class="room-price">₱5,500 <sub>/ night</sub></div>
                        <button class="btn-room">View Room</button>
                    </div>
                </div>
            </div>

            <div class="room-card reveal reveal-delay-1">
                <div class="room-card-img">
                    <div class="room-card-img-bg r-img-5">
                        <img src="assets/images/OGV 5th Unit A.jpg" alt="OGV 5th Unit H">
                    </div>
                    <span class="room-badge new">Heritage</span>
                </div>
                <div class="room-card-body">
                    <div class="room-name">Bahay Kubo Suite</div>
                    <div class="room-meta">
                        <span><svg viewBox="0 0 24 24">
                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2" />
                            </svg>Courtyard</span>
                        <span><svg viewBox="0 0 24 24">
                                <rect x="2" y="7" width="20" height="14" rx="2" />
                                <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" />
                            </svg>1 King Bed</span>
                        <span><svg viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                            </svg>2 Guests</span>
                    </div>
                    <div class="room-divider"></div>
                    <div class="room-price-row">
                        <div class="room-price">₱3,800 <sub>/ night</sub></div>
                        <button class="btn-room">View Room</button>
                    </div>
                </div>
            </div>

            <div class="room-card reveal reveal-delay-2">
                <div class="room-card-img">
                    <div class="room-card-img-bg r-img-6">
                        <img src="assets/images/unit A18.jpg" alt="Unit A18">
                    </div>
                    <span class="room-badge popular">Premium</span>
                </div>
                <div class="room-card-body">
                    <div class="room-name">Sampaguita Premiere</div>
                    <div class="room-meta">
                        <span><svg viewBox="0 0 24 24">
                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2" />
                            </svg>City View</span>
                        <span><svg viewBox="0 0 24 24">
                                <rect x="2" y="7" width="20" height="14" rx="2" />
                                <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" />
                            </svg>1 King Bed</span>
                        <span><svg viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                            </svg>2 Guests</span>
                    </div>
                    <div class="room-divider"></div>
                    <div class="room-price-row">
                        <div class="room-price">₱4,800 <sub>/ night</sub></div>
                        <button class="btn-room">View Room</button>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section class="testimonials" id="reviews">
        <div class="testimonials-header reveal">
            <div class="eyebrow">Guest Reviews</div>
            <h2 class="section-heading" style="color:var(--white);">What Our Guests <em
                    style="color:var(--gold)">Say</em></h2>
        </div>

        <div class="testi-grid">

            <div class="testi-card reveal">
                <span class="testi-quote-icon">"</span>
                <div class="testi-stars">
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                </div>
                <p class="testi-text">Staying at Filipino Homes was the highlight of our Boracay trip. The Ocean View
                    Apartment felt luxurious and comfortable — spacious rooms, modern furnishings, and the most
                    comfortable bed I've ever slept in. The staff treated us like family from the moment we arrived.</p>
                <div class="testi-author">
                    <div class="testi-avatar av-1">M</div>
                    <div>
                        <div class="testi-name">Maria & Jose R.</div>
                        <div class="testi-location">Manila, Philippines</div>
                    </div>
                </div>
            </div>

            <div class="testi-card reveal reveal-delay-1">
                <span class="testi-quote-icon">"</span>
                <div class="testi-stars">
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                </div>
                <p class="testi-text">The balcony views were breathtaking — waking up to the turquoise waters of Boracay
                    every morning was magical. Breakfast included local favorites and fresh pastries. Absolutely
                    perfect.</p>
                <div class="testi-author">
                    <div class="testi-avatar av-2">L</div>
                    <div>
                        <div class="testi-name">Liam Chen</div>
                        <div class="testi-location">Singapore</div>
                    </div>
                </div>
            </div>

            <div class="testi-card reveal reveal-delay-2">
                <span class="testi-quote-icon">"</span>
                <div class="testi-stars">
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                </div>
                <p class="testi-text">We booked the Family Loft Apartment for our family of four, and it was just right.
                    The kids loved the space, and we loved the beach view. Kuya Ren at the front desk was incredibly
                    helpful — he arranged a tricycle tour of the island for us the very next morning!</p>
                <div class="testi-author">
                    <div class="testi-avatar av-4">A</div>
                    <div>
                        <div class="testi-name">Anna Kowalski</div>
                        <div class="testi-location">Warsaw, Poland</div>
                    </div>
                </div>
            </div>

            <div class="testi-card reveal">
                <span class="testi-quote-icon">"</span>
                <div class="testi-stars">
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                </div>
                <p class="testi-text">The Deluxe Seaview Apartment was pure bliss. The modern interiors, thoughtful
                    details, and fresh tropical air made our stay unforgettable. I will return every year — Filipino
                    Homes has set a new standard for comfort and hospitality.</p>
                <div class="testi-author">
                    <div class="testi-avatar av-3">S</div>
                    <div>
                        <div class="testi-name">Sofia Delgado</div>
                        <div class="testi-location">Barcelona, Spain</div>
                    </div>
                </div>
            </div>

            <div class="testi-card reveal reveal-delay-1">
                <span class="testi-quote-icon">"</span>
                <div class="testi-stars">
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                </div>
                <p class="testi-text">As a travel blogger, I've stayed in hundreds of places, and Filipino Homes
                    genuinely impressed me. The attention to detail in the Sampaguita Premier Apartment is something you
                    rarely see. The breakfast spread with fresh pan de sal and tablea tsokolate sealed the deal.</p>
                <div class="testi-author">
                    <div class="testi-avatar av-5">J</div>
                    <div>
                        <div class="testi-name">Jake Morrison</div>
                        <div class="testi-location">Sydney, Australia</div>
                    </div>
                </div>
            </div>

            <div class="testi-card reveal reveal-delay-2">
                <span class="testi-quote-icon">"</span>
                <div class="testi-stars">
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                </div>
                <p class="testi-text">Kumpleto ang lahat! Malinis, maganda, at mainit ang pagtanggap. The Garden Breeze
                    Apartment’s balcony had the perfect view every morning. Our whole barkada is already planning a
                    return trip — Filipino Homes feels like a second home to us now.</p>
                <div class="testi-author">
                    <div class="testi-avatar av-6">R</div>
                    <div>
                        <div class="testi-name">Rhea Villanueva</div>
                        <div class="testi-location">Cebu, Philippines</div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section class="cta-section" id="cta">
        <div class="cta-pattern"></div>
        <div class="eyebrow cta-eyebrow reveal" style="justify-content:center;">
            <span style="width:24px;height:1.5px;background:var(--gold);display:block;"></span>
            &nbsp; Start Your Journey
        </div>
        <h2 class="cta-heading reveal">
            Where Every Stay<br>Feels Like <em>Coming Home</em>
        </h2>
        <p class="cta-sub reveal">
            From the gentle sea breeze at dawn to the glow of Boracay’s sunsets, your most memorable Filipino experience
            awaits. Book your stay at Filipino Homes in Boracay today.
        </p>
        <button class="btn-book-big reveal">
            Book Your Stay Now
            <svg viewBox="0 0 24 24">
                <line x1="5" y1="12" x2="19" y2="12" />
                <polyline points="12 5 19 12 12 19" />
            </svg>
        </button>
        <p class="cta-note reveal">Free cancellation up to 48 hours before check-in · No hidden fees</p>
    </section>

    <footer id="contact">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="#" class="logo" style="margin-bottom:1rem;display:inline-flex;">
                    <img src="assets/images/logo.png" alt="Filipino Homes Logo" class="logo-icon-2">
                    Filipino Homes
                </a>
                <p>A modern apartment retreat on Boracay, blending authentic Filipino warmth with contemporary comfort.
                </p>
                <div class="social-row">
                    <a href="#" class="soc" aria-label="Facebook">
                        <svg fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z" />
                        </svg>
                    </a>
                    <a href="#" class="soc" aria-label="Instagram">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="2" y="2" width="20" height="20" rx="5" />
                            <circle cx="12" cy="12" r="4" />
                            <circle cx="17.5" cy="6.5" r=".5" fill="currentColor" />
                        </svg>
                    </a>
                    <a href="#" class="soc" aria-label="TripAdvisor">
                        <svg fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18a8 8 0 110-16 8 8 0 010 16zm-1-5a1 1 0 102 0 1 1 0 00-2 0zm-4-4a1 1 0 102 0 1 1 0 00-2 0zm8 0a1 1 0 102 0 1 1 0 00-2 0z" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="footer-col">
                <h5>Navigate</h5>
                <ul class="footer-links">
                    <li><a href="#hero">Home</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#rooms">Rooms</a></li>
                    <li><a href="#reviews">Reviews</a></li>
                    <li><a href="#cta">Book Now</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5>Contact Us</h5>
                <div class="contact-row">
                    <svg viewBox="0 0 24 24">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                    Station 3, Barangay Manoc-Manoc,<br>Boracay Island, Aklan 5608
                </div>
                <div class="contact-row">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" />
                    </svg>
                    +63 33 123 4567
                </div>
                <div class="contact-row">
                    <svg viewBox="0 0 24 24">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                        <polyline points="22,6 12,13 2,6" />
                    </svg>
                    hello@filipinohomes.ph
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <span>© 2025 Filipino Homes Investment Properties & Services. All rights reserved.</span>
            <span>Privacy Policy · Terms · Booking Policy</span>
        </div>
    </footer>

    <script src="assets/js/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php
    if (isset($_SESSION['alert'])) {
        $type = $_SESSION['alert']['type']; 
        $message = $_SESSION['alert']['message'];
        echo "<script>
            Swal.fire({
                icon: '$type',
                title: '$message',
                timer: 1750,
                confirmButtonColor: '#3085d6',
            });
        </script>";
        unset($_SESSION['alert']); 
    }
    ?>

</body>
</html>