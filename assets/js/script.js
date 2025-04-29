//********************** for menu  ********************** //
const navBtn = document.querySelector(".nav__btn");
const navMenu = document.querySelector(".nav-menu");
const nav = document.querySelector(".nav");

let navOpen = false;

navBtn.addEventListener("click", function () {
    navBtn.classList.toggle("nav__btn--open");
    navMenu.classList.toggle("nav-menu--open");
    nav.classList.toggle("nav--change-color");
    navOpen = !navOpen;
});



//********************** for the dropDown ********************** //

document.addEventListener("DOMContentLoaded", function () {
    const toggleDropdown = document.getElementById("toggleDropdown");
    const userDropdown = document.getElementById("userDropdown");
    const toggleIcon = document.getElementById("toggleIcon");

    toggleDropdown.addEventListener("click", function (event) {
        event.stopPropagation();
        userDropdown.classList.toggle("active");
        toggleIcon.classList.toggle("fa-plus");
        toggleIcon.classList.toggle("fa-minus");
    });

    document.addEventListener("click", function (event) {
        if (!toggleDropdown.contains(event.target) && !userDropdown.contains(event.target)) {
            userDropdown.classList.remove("active");
            toggleIcon.classList.add("fa-plus");
            toggleIcon.classList.remove("fa-minus");
        }
    });
});

//********************** Slider ********************** //
const slides = document.querySelector('.slides');
const dots = document.querySelectorAll('.dot');
let currentIndex = 0;
const totalSlides = document.querySelectorAll('.slide').length;

function changeSlide(index) {
    currentIndex = index;
    updateSlider();
}

function updateSlider() {
    slides.style.transform = `translateX(-${currentIndex * 100}%)`;

    dots.forEach(dot => dot.classList.remove('active'));
    dots[currentIndex].classList.add('active');
}

// Automatic slide change every 3 seconds
setInterval(() => {
    currentIndex = (currentIndex + 1) % totalSlides;
    updateSlider();
}, 3000);

// Add click event to dots
dots.forEach((dot, index) => {
    dot.addEventListener("click", () => changeSlide(index));
});

// Initialize the first slide and dot
updateSlider();

//********************** toggle the "More" text ********************** //
function toggleDescription() {
    var description = document.getElementById("companyDescription");
    var btnText = document.getElementById("toggleBtn");

    if (description.style.display === "none") {
        // Show the company description and change button text to "Moins..."
        description.style.display = "block";
        btnText.innerHTML = "Moins...";
    } else {
        // Hide the company description and change button text to "Plus..."
        description.style.display = "none";
        btnText.innerHTML = "Plus...";
    }
}
//********************** message in create page ********************** //

    setTimeout(() => {
    const msg = document.querySelector('.flash-message');
    if (msg) msg.style.display = 'none';
}, 4000); // 4 seconds



