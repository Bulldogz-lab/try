const loginModal = document.getElementById('loginModal');

function openModal(tab = 'login') {
    loginModal.classList.add('open');
    document.body.style.overflow = 'hidden';
    switchTab(tab);
}
function closeModal() {
    loginModal.classList.remove('open');
    document.body.style.overflow = '';
}
function switchTab(tab) {
    document.querySelectorAll('.modal-tab').forEach(t =>
        t.classList.toggle('active', t.dataset.tab === tab));
    document.querySelectorAll('.modal-form').forEach(f =>
        f.classList.toggle('active', f.id === 'tab-' + tab));
}

document.getElementById('modalClose').addEventListener('click', closeModal);
loginModal.addEventListener('click', e => { if (e.target === loginModal) closeModal(); });

document.querySelector('.btn-login-header').addEventListener('click', () => openModal('login'));
document.querySelector('.btn-book-header').addEventListener('click', () => openModal('login'));

document.querySelectorAll('.btn-room').forEach(btn =>
    btn.addEventListener('click', () => openModal('login')));

document.querySelector('.btn-book-big').addEventListener('click', () => openModal('login'));

const hdr = document.getElementById('hdr');
window.addEventListener('scroll', () => hdr.classList.toggle('scrolled', scrollY > 20));

const burger = document.getElementById('hamburger');
const mob = document.getElementById('mobileNav');
let mobOpen = false;

burger.addEventListener('click', () => {
    mobOpen = !mobOpen;
    mob.classList.toggle('open', mobOpen);
    const s = burger.querySelectorAll('span');
    if (mobOpen) {
        s[0].style.transform = 'translateY(6.5px) rotate(45deg)';
        s[1].style.opacity = '0';
        s[2].style.transform = 'translateY(-6.5px) rotate(-45deg)';
    } else resetB();
});
function resetB() {
    burger.querySelectorAll('span').forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
}
function closeMob() { mobOpen = false; mob.classList.remove('open'); resetB(); }

const slides = document.querySelectorAll('.carousel-slide');
const dots = document.querySelectorAll('.carousel-dots .dot');
const thumbs = document.querySelectorAll('.carousel-thumbs .thumb');
const slideWrap = document.getElementById('carouselSlides');
const labelName = document.getElementById('slideLabel');
const labelType = document.getElementById('slideType');

let current = 0;
let autoTimer = null;

function goTo(idx) {
    slides[current].classList.remove('active');
    dots[current].classList.remove('active');
    thumbs[current].classList.remove('active');
    current = (idx + slides.length) % slides.length;
    slides[current].classList.add('active');
    dots[current].classList.add('active');
    thumbs[current].classList.add('active');
    slideWrap.style.transform = `translateX(-${current * 100}%)`;
    const slide = slides[current];
    labelName.textContent = slide.dataset.label;
    labelType.textContent = slide.dataset.type;
    resetAuto();
}

function resetAuto() {
    clearInterval(autoTimer);
    autoTimer = setInterval(() => goTo(current + 1), 5000);
}

document.getElementById('nextBtn').addEventListener('click', () => goTo(current + 1));
document.getElementById('prevBtn').addEventListener('click', () => goTo(current - 1));
dots.forEach(d => d.addEventListener('click', () => goTo(+d.dataset.idx)));
thumbs.forEach(t => t.addEventListener('click', () => goTo(+t.dataset.idx)));

let touchStartX = 0;
const frame = document.getElementById('carouselFrame');
frame.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
frame.addEventListener('touchend', e => {
    const diff = touchStartX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 40) goTo(diff > 0 ? current + 1 : current - 1);
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeModal(); return; }
    if (loginModal.classList.contains('open')) return;
    if (e.key === 'ArrowRight') goTo(current + 1);
    if (e.key === 'ArrowLeft') goTo(current - 1);
});

resetAuto();

const revealObs = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) { e.target.classList.add('visible'); revealObs.unobserve(e.target); }
    });
}, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

document.querySelectorAll('.reveal').forEach(el => revealObs.observe(el));

async function verifyOtp(attempts = 0) {
    const maxAttempts = 3;

    if (attempts >= maxAttempts) {
        Swal.fire({
            icon: 'error',
            title: 'Too many incorrect attempts.',
            text: 'Please log in again.',
            showConfirmButton: true
        });
        return;
    }

    const { value: otp } = await Swal.fire({
        title: 'Enter OTP',
        text: `OTP sent to your email. Attempts remaining: ${maxAttempts - attempts}`,
        input: 'text',
        inputAttributes: {
            maxlength: 6,
            autocomplete: 'one-time-code',
            placeholder: '000000'
        },
        inputValidator: (value) => {
            if (!value || value.length !== 6) return 'Please enter the 6-digit OTP!';
        },
        confirmButtonText: 'Verify',
        showCancelButton: true,
        allowOutsideClick: false
    });

    if (!otp) return;

    const res = await fetch('process/verify_login_otp.php', {
        method: 'POST',
        body: new URLSearchParams({ otp })
    });

    const data = await res.json();

    if (data.status === 'success') {
        Swal.fire({
            icon: 'success',
            title: data.message,
            timer: 1200,
            showConfirmButton: false
        });

        setTimeout(() => {
            window.location.href = data.role === 'admin'
                ? 'pages/admin/index.php'
                : 'pages/user/user-dashboard.php';
        }, 1200);

    } else {
        await Swal.fire({
            icon: 'error',
            title: data.message,
            text: `${maxAttempts - attempts - 1} attempt(s) remaining.`,
            timer: 1500,
            showConfirmButton: false
        });

        verifyOtp(attempts + 1);
    }
}

loginForm.addEventListener('submit', async function (e) {
    e.preventDefault();

    const formData = new FormData(loginForm);
    formData.append('login', true);

    try {
        const response = await fetch('process/login.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.status === 'otp_sent') {
            await Swal.fire({
                icon: 'success',
                title: data.message,
                timer: 1200,
                showConfirmButton: false
            });
            verifyOtp();

        } else if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: data.message,
                timer: 1200,
                showConfirmButton: false
            });
            setTimeout(() => {
                window.location.href = data.role === 'admin'
                    ? 'pages/admin/index.php'
                    : 'pages/user/user-dashboard.php';
            }, 1200);

        } else {
            Swal.fire({
                icon: 'error',
                title: data.message,
                toast: true,
                position: 'top',
                timer: 2000,
                showConfirmButton: false
            });
        }

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Server error!',
            text: 'Please try again later.'
        });
    }
});

const registerForm = document.getElementById('registerForm');

registerForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(registerForm);
    formData.append('register', true);

    try {
        const response = await fetch('process/register.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                switchTab('login');
                registerForm.reset();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: data.message,
                toast: true,
                position: 'top',
                timer: 2500,
                showConfirmButton: false
            });
        }

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Server error!',
            text: 'Please try again later.'
        });
    }
});