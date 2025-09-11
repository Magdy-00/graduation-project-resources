// Enhanced JavaScript for RefCollect

function openForm() {
	const popup = document.getElementById("form-popup");
	popup.style.display = "flex";
	// Focus on first input
	setTimeout(() => {
		const firstInput = popup.querySelector('input[type="text"]');
		if (firstInput) firstInput.focus();
	}, 100);
}

function closeForm() {
	document.getElementById("form-popup").style.display = "none";
}

function filterCategory(category) {
	const cards = document.querySelectorAll(".card");
	const buttons = document.querySelectorAll(".categories button");

	// Update active button
	buttons.forEach((btn) => btn.classList.remove("active"));
	event.target.classList.add("active");

	// Filter cards with animation
	cards.forEach((card) => {
		if (category === "all" || card.dataset.category === category) {
			card.style.display = "block";
			card.style.opacity = "0";
			setTimeout(() => (card.style.opacity = "1"), 50);
		} else {
			card.style.opacity = "0";
			setTimeout(() => (card.style.display = "none"), 300);
		}
	});

	// Update results count
	updateResultsCount();
}

function updateResultsCount() {
	const visibleCards = document.querySelectorAll(
		".card[style*='display: block'], .card:not([style*='display: none'])"
	);
	const countElement = document.querySelector("#results-count");
	if (countElement) {
		countElement.textContent = `${visibleCards.length} reference(s)`;
	}
}

// Enhanced search functionality
document.getElementById("search").addEventListener("input", function () {
	const query = this.value.toLowerCase().trim();
	const cards = document.querySelectorAll(".card");

	cards.forEach((card) => {
		const text = card.textContent.toLowerCase();
		const isVisible = text.includes(query);

		if (isVisible) {
			card.style.display = "block";
			card.style.opacity = "1";
			// Highlight search terms
			highlightSearchTerms(card, query);
		} else {
			card.style.opacity = "0";
			setTimeout(() => (card.style.display = "none"), 300);
		}
	});

	updateResultsCount();
});

function highlightSearchTerms(card, query) {
	if (!query) return;

	// Simple highlight functionality
	const title = card.querySelector("h3");
	if (title) {
		let html = title.innerHTML;
		const regex = new RegExp(`(${query})`, "gi");
		html = html.replace(regex, "<mark>$1</mark>");
		title.innerHTML = html;
	}
}

// Close popup with Escape key
document.addEventListener("keydown", function (e) {
	if (e.key === "Escape") {
		closeForm();
	}
});

// Close popup when clicking outside
document.getElementById("form-popup").addEventListener("click", function (e) {
	if (e.target === this) {
		closeForm();
	}
});

// Auto-expand comment textareas
document.addEventListener("DOMContentLoaded", function () {
	const textareas = document.querySelectorAll(".add-comment textarea");
	textareas.forEach((textarea) => {
		textarea.addEventListener("input", function () {
			this.style.height = "auto";
			this.style.height = this.scrollHeight + "px";
		});
	});

	// Set first category as active
	const firstButton = document.querySelector(".categories button");
	if (firstButton) firstButton.classList.add("active");

	// Initial count
	updateResultsCount();

	// Initialize theme
	initializeTheme();
});

// Theme toggle functionality
function toggleTheme() {
	const currentTheme = localStorage.getItem("theme") || "light";
	const newTheme = currentTheme === "light" ? "dark" : "light";

	setTheme(newTheme);
	localStorage.setItem("theme", newTheme);
}

function setTheme(theme) {
	document.documentElement.setAttribute("data-theme", theme);
}

function initializeTheme() {
	// Check for saved theme preference or default to light mode
	const savedTheme = localStorage.getItem("theme");

	let theme = savedTheme || "light"; // Default to light mode

	setTheme(theme);

	// Listen for system theme changes only if no theme is saved
	window
		.matchMedia("(prefers-color-scheme: dark)")
		.addEventListener("change", (e) => {
			if (!localStorage.getItem("theme")) {
				// Even with system changes, keep light as default
				setTheme("light");
			}
		});
}

// Smooth scroll to added reference
if (window.location.hash) {
	setTimeout(() => {
		const element = document.querySelector(window.location.hash);
		if (element) {
			element.scrollIntoView({ behavior: "smooth", block: "center" });
		}
	}, 500);
}
