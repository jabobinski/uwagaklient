document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".toggle-image-btn").forEach(button => {
        button.addEventListener("click", function() {
            const index = button.dataset.index;
            const imageDiv = document.getElementById(`image-${index}`);
            if (imageDiv.style.maxHeight) {
                imageDiv.style.maxHeight = null;
            } else {
                imageDiv.style.maxHeight = imageDiv.scrollHeight + "px";
            }
        });
    });

    document.querySelectorAll(".post button").forEach(button => {
        button.addEventListener("click", function() {
            const index = button.closest(".post").dataset.index;
            const form = document.getElementById(`comment-form-${index}`);
            if (form.style.maxHeight) {
                form.style.maxHeight = null;
            } else {
                form.style.maxHeight = form.scrollHeight + "px";
            }
        });
    });

    document.querySelectorAll(".rating button").forEach(button => {
        button.addEventListener("click", function() {
            const postIndex = button.closest(".post").dataset.index;
            const value = button.classList.contains("upvote") ? 1 : -1;

            fetch("rate_post.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ postIndex, value })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const score = button.closest(".rating").querySelector(".score");
                    score.textContent = data.newScore;
                }
            });
        });
    });
});
