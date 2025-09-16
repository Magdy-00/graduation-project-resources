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

function openEditForm() {
	const popup = document.getElementById("edit-form-popup");
	popup.style.display = "flex";
	// Focus on first input
	setTimeout(() => {
		const firstInput = popup.querySelector('input[type="text"]');
		if (firstInput) firstInput.focus();
	}, 100);
}

function closeEditForm() {
	document.getElementById("edit-form-popup").style.display = "none";
}

// Dropdown menu functionality
function toggleDropdown(referenceId) {
	console.log("toggleDropdown called with ID:", referenceId);

	const dropdown = document.getElementById(`dropdown-${referenceId}`);
	if (!dropdown) {
		console.error("Dropdown not found for ID:", referenceId);
		return;
	}

	// Close all other dropdowns
	const allDropdowns = document.querySelectorAll(".dropdown-content");
	allDropdowns.forEach((dd) => {
		if (dd !== dropdown) {
			dd.classList.remove("show");
		}
	});

	// Toggle current dropdown
	dropdown.classList.toggle("show");
	console.log(
		"Dropdown is now",
		dropdown.classList.contains("show") ? "visible" : "hidden"
	);
}

// Close dropdowns when clicking outside
document.addEventListener("click", function (event) {
	// Don't close if clicking on a dropdown toggle button or inside dropdown content
	if (!event.target.closest(".dropdown-menu")) {
		const allDropdowns = document.querySelectorAll(".dropdown-content");
		allDropdowns.forEach((dropdown) => {
			dropdown.classList.remove("show");
		});
	}
});

// Edit reference functionality
function editReference(referenceId) {
	console.log("editReference called with ID:", referenceId);

	// Close dropdown
	const dropdown = document.getElementById(`dropdown-${referenceId}`);
	if (dropdown) {
		dropdown.classList.remove("show");
	}

	// Get reference data from the card
	const card = document.getElementById(`ref-${referenceId}`);
	if (!card) {
		console.error("Card not found for reference ID:", referenceId);
		alert("Error: Could not find reference card");
		return;
	}

	try {
		// Get title from the h3 element (first text node)
		const h3Element = card.querySelector("h3");
		if (!h3Element) {
			throw new Error("Title element not found");
		}

		// Get the text content and clean it up
		let title = h3Element.childNodes[0]
			? h3Element.childNodes[0].textContent.trim()
			: h3Element.textContent.trim();
		title = title.replace(/🔗/g, "").trim(); // Remove link emoji if present

		// Get comment from the correct paragraph (skip the "By:" paragraph)
		const paragraphs = card.querySelectorAll("p");
		let comment = "";
		// Find the paragraph that contains the actual comment (not the "By:" paragraph)
		for (let i = 0; i < paragraphs.length; i++) {
			const pText = paragraphs[i].textContent.trim();
			// Skip the "By:" paragraph and any empty paragraphs
			if (!pText.startsWith("By:") && pText.length > 0) {
				comment = pText;
				break;
			}
		}

		// Get category from the tag
		const tagElement = card.querySelector(".tag");
		const category = tagElement ? tagElement.textContent.trim() : "";

		// Get URL from the link if it exists
		const urlLink = card.querySelector('h3 a[target="_blank"]');
		const url = urlLink ? urlLink.href : "";

		console.log("Extracted data:", { title, comment, category, url });

		// Populate edit form
		document.getElementById("edit-reference-id").value = referenceId;
		document.getElementById("edit-title").value = title;
		document.getElementById("edit-url").value = url;
		document.getElementById("edit-comment").value = comment;
		document.getElementById("edit-category").value = category;

		// Log the form values to verify they were set correctly
		console.log("Form populated with:", {
			id: document.getElementById("edit-reference-id").value,
			title: document.getElementById("edit-title").value,
			url: document.getElementById("edit-url").value,
			comment: document.getElementById("edit-comment").value,
			category: document.getElementById("edit-category").value,
		});

		// Open edit form
		openEditForm();
	} catch (error) {
		console.error("Error extracting reference data:", error);
		alert("Error: Could not extract reference data");
	}
}

// Delete reference functionality
function deleteReference(referenceId) {
	console.log("deleteReference called with ID:", referenceId);

	// Close dropdown
	const dropdown = document.getElementById(`dropdown-${referenceId}`);
	if (dropdown) {
		dropdown.classList.remove("show");
	}

	// Get reference title for confirmation
	const card = document.getElementById(`ref-${referenceId}`);
	if (!card) {
		console.error("Card not found for reference ID:", referenceId);
		alert("Error: Could not find reference card");
		return;
	}

	try {
		// Get title from the h3 element
		const h3Element = card.querySelector("h3");
		if (!h3Element) {
			throw new Error("Title element not found");
		}

		let title = h3Element.childNodes[0]
			? h3Element.childNodes[0].textContent.trim()
			: h3Element.textContent.trim();
		title = title.replace(/🔗/g, "").trim(); // Remove link emoji if present

		console.log("Deleting reference with title:", title);

		// Confirm deletion
		if (
			confirm(
				`Are you sure you want to delete "${title}"?\n\nThis action cannot be undone.`
			)
		) {
			// Create form and submit
			const form = document.createElement("form");
			form.method = "POST";
			form.action = "delete_reference.php";
			form.style.display = "none";

			const input = document.createElement("input");
			input.type = "hidden";
			input.name = "reference_id";
			input.value = referenceId;

			form.appendChild(input);
			document.body.appendChild(form);

			console.log("Submitting delete form for reference ID:", referenceId);
			form.submit();
		}
	} catch (error) {
		console.error("Error in deleteReference:", error);
		alert("Error: Could not delete reference");
	}
}

// Make functions globally accessible
window.toggleDropdown = toggleDropdown;
window.editReference = editReference;
window.deleteReference = deleteReference;

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
		closeEditForm();
	}
});

// Close popup when clicking outside
document.getElementById("form-popup").addEventListener("click", function (e) {
	if (e.target === this) {
		closeForm();
	}
});

// Close edit popup when clicking outside
document.addEventListener("DOMContentLoaded", function () {
	const editPopup = document.getElementById("edit-form-popup");
	if (editPopup) {
		editPopup.addEventListener("click", function (e) {
			if (e.target === this) {
				closeEditForm();
			}
		});
	}
});

// Auto-expand comment textareas
document.addEventListener("DOMContentLoaded", function () {
	console.log("DOM Content Loaded");

	// Check if dropdown buttons exist
	const dropdownButtons = document.querySelectorAll(".dropdown-toggle");
	console.log("Dropdown buttons found:", dropdownButtons.length);

	// Ensure dropdown functionality works (the onclick attributes should handle the primary functionality)
	dropdownButtons.forEach((button, index) => {
		console.log(`Dropdown button ${index}:`, button);
		console.log(`Onclick attribute:`, button.getAttribute("onclick"));
	});

	// Check if dropdown content exists
	const dropdownContents = document.querySelectorAll(".dropdown-content");
	console.log("Dropdown contents found:", dropdownContents.length);

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
