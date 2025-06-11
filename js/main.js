document.addEventListener("DOMContentLoaded", function () {

    let currentFilter = "All"; 
    let currentSort = "default";

    // ------------------------
    // Load locations dropdown
    // ------------------------
    function loadLocations() {
        fetch("php/get_locations.php")
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Locations data:', data); // Debug log
                
                if (!data.success) {
                    throw new Error(data.message || 'Failed to load locations');
                }
                
                const locationList = document.getElementById("location-list");
                if (!locationList) {
                    throw new Error('Location select element not found');
                }
                
                // Clear existing options except the first one
                locationList.innerHTML = '<option value="">Select a location</option>';
                
                if (!data.locations || !Array.isArray(data.locations)) {
                    throw new Error('Invalid locations data received');
                }
                
                data.locations.forEach(location => {
                    if (!location.id || !location.name) {
                        console.warn('Invalid location data:', location);
                        return;
                    }
                    
                    const option = document.createElement("option");
                    option.value = location.name;
                    option.textContent = location.name;
                    locationList.appendChild(option);
                });
            })
            .catch(error => {
                console.error("Error loading locations:", error);
                const locationList = document.getElementById("location-list");
                if (locationList) {
                    locationList.innerHTML = '<option value="">Error loading locations</option>';
                }
            });
    }

    loadLocations();

    // ------------------------
    // Check availability
    // ------------------------
    document.getElementById("search-form")?.addEventListener("submit", function (e) {
        e.preventDefault();
        const location = document.getElementById("location-list").value;
        const startDate = document.getElementById("pickup-date").value;
        const endDate = document.getElementById("return-date").value;

        if (!location || !startDate || !endDate) {
            alert("🚫 Please fill in all fields.");
            return;
        }

        fetch("php/check_availability.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `location=${encodeURIComponent(location)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(`⚠️ ${data.error}`);
                } else {
                    alert(`✅ ${data.available_cars} car(s) available.`);
                    setTimeout(() => {
                        document.getElementById("feature")?.scrollIntoView({ behavior: "smooth" });
                    }, 100);
                }
            })
            .catch(error => {
                console.error("Error checking availability:", error);
                alert("❌ Could not check availability.");
            });
    });

    // ------------------------
    // Check login status
    // ------------------------
    function checkLoginStatus() {
        const isLoggedIn = localStorage.getItem('loggedIn') === 'true';
        const userName = localStorage.getItem('userName');
        const profileWrapper = document.getElementById('profile-dropdown');
        const signUpBtn = document.getElementById('signup-btn');
        const signInBtn = document.getElementById('signin-btn');
        const userNameSpan = document.getElementById('user-name');

        if (isLoggedIn && userName) {
            profileWrapper?.classList.remove('hidden');
            signUpBtn && (signUpBtn.style.display = 'none');
            signInBtn && (signInBtn.style.display = 'none');
            userNameSpan && (userNameSpan.innerHTML = `👤 ${userName}`);
        } else {
            profileWrapper?.classList.add('hidden');
            signUpBtn && (signUpBtn.style.display = 'inline-block');
            signInBtn && (signInBtn.style.display = 'inline-block');
            userNameSpan && (userNameSpan.innerHTML = '');
        }
    }

    document.getElementById('logout-btn')?.addEventListener('click', function () {
        localStorage.setItem('loggedIn', 'false');
        localStorage.removeItem('userName');
        window.location.href = 'index.html';
    });

    checkLoginStatus();

    // ------------------------
    // Load cars
    // ------------------------
    function loadCars() {
        fetch(`php/fetch_cars.php?type=${encodeURIComponent(currentFilter)}&sort=${encodeURIComponent(currentSort)}`)
            .then(res => res.json())
            .then(cars => {
                const container = document.getElementById("carContainer");
                container.innerHTML = "";

                if (!cars || cars.length === 0) {
                    container.innerHTML = "<p style='color:white;'>No cars found.</p>";
                    return;
                }

                cars.forEach(car => {
                    const card = document.createElement("div");
                    card.className = "car-card";
                    card.innerHTML = `
                        ${car.featured == 1 ? '<div class="featured-badge">Featured</div>' : ''}
                        <div class="car-image">
                            <img src="img/cars/${car.image}" alt="${car.name}" onerror="this.onerror=null; this.src='img/cars/default.png';">
                        </div>
                        <div class="car-info">
                            <h3>
                                <span>${car.name}</span>
                                <span class="rating"><i class="ri-star-fill"></i> ${car.rating}</span>
                            </h3>
                            <div class="specs-row">
                                <span class="spec-left">${car.seats} Seats</span>
                                <span class="spec-center">${car.transmission}</span>
                                <span class="spec-right">${car.category}</span>
                            </div>
                            <div class="price">₹${car.price} <span>/day</span></div>
                            <button class="view-btn" onclick="window.location.href='html/car_details.html?id=${car.id}'">View Details</button>
                        </div>
                    `;
                    container.appendChild(card);
                });
            })
            .catch(err => {
                console.error("Error loading cars:", err);
            });
    }

    loadCars();

    // ------------------------
    // Filters
    // ------------------------
    document.querySelectorAll(".filter-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            currentFilter = btn.getAttribute("data-category") || "All";
            document.querySelectorAll(".filter-btn").forEach(button => button.classList.remove("active"));
            btn.classList.add("active");
            loadCars();
        });
    });

    // ------------------------
    // Sort Dropdown (Hover and Select)
    // ------------------------
    const dropdown = document.querySelector(".sortdropdown");
    const selected = dropdown.querySelector(".selected");
    const options = dropdown.querySelector(".dropdown-options");

    dropdown.addEventListener("mouseenter", () => {
        dropdown.classList.add("open");
    });

    dropdown.addEventListener("mouseleave", () => {
        dropdown.classList.remove("open");
    });

    options.querySelectorAll("li").forEach(option => {
        option.addEventListener("click", () => {
            const value = option.getAttribute("data-value");
            const text = option.textContent;

            selected.textContent = text;
            currentSort = value;
            dropdown.classList.remove("open");
            loadCars(); 
            
        });
        
    });    

    // ------------------------
    // Reviews Slider
    // ------------------------
    const track = document.querySelector('.reviews-track');
    const cards = document.querySelectorAll('.review-card');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    const dotsContainer = document.querySelector('.slider-dots');
    
    if (track && cards.length > 0) {
        let currentIndex = 0;
        const cardWidth = cards[0].offsetWidth + 30; // Including gap
        const cardsPerView = Math.floor(track.offsetWidth / cardWidth);
        
        // Create dots
        cards.forEach((_, index) => {
            const dot = document.createElement('div');
            dot.classList.add('dot');
            if (index === 0) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(index));
            dotsContainer.appendChild(dot);
        });
        
        const dots = document.querySelectorAll('.dot');
        
        function updateSlider() {
            const offset = -currentIndex * cardWidth;
            track.style.transform = `translateX(${offset}px)`;
            
            // Update dots
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentIndex);
            });
            
            // Update button states
            prevBtn.style.opacity = currentIndex === 0 ? '0.5' : '1';
            nextBtn.style.opacity = currentIndex >= cards.length - cardsPerView ? '0.5' : '1';
        }
        
        function goToSlide(index) {
            currentIndex = index;
            updateSlider();
        }
        
        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                updateSlider();
            }
        });
        
        nextBtn.addEventListener('click', () => {
            if (currentIndex < cards.length - cardsPerView) {
                currentIndex++;
                updateSlider();
            }
        });
        
        // Auto slide every 5 seconds
        let autoSlideInterval = setInterval(() => {
            if (currentIndex < cards.length - cardsPerView) {
                currentIndex++;
            } else {
                currentIndex = 0;
            }
            updateSlider();
        }, 3000);
        
        // Pause auto slide on hover
        track.addEventListener('mouseenter', () => {
            clearInterval(autoSlideInterval);
        });
        
        track.addEventListener('mouseleave', () => {
            autoSlideInterval = setInterval(() => {
                if (currentIndex < cards.length - cardsPerView) {
                    currentIndex++;
                } else {
                    currentIndex = 0;
                }
                updateSlider();
            }, 3000);
        });
        
        // Initial setup
        updateSlider();
        
        // Update on window resize
        window.addEventListener('resize', () => {
            const newCardWidth = cards[0].offsetWidth + 30;
            const newCardsPerView = Math.floor(track.offsetWidth / newCardWidth);
            if (currentIndex > cards.length - newCardsPerView) {
                currentIndex = Math.max(0, cards.length - newCardsPerView);
            }
            updateSlider();
        });
    }
});
