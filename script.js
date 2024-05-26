document.addEventListener('DOMContentLoaded', () => {
    const posts = document.querySelectorAll('.post');

    posts.forEach(post => {
        const upvoteButton = post.querySelector('.upvote');
        const downvoteButton = post.querySelector('.downvote');
        const scoreElement = post.querySelector('.score');

        let score = parseInt(scoreElement.textContent, 10);

        upvoteButton.addEventListener('click', () => {
            score++;
            scoreElement.textContent = score;
        });

        downvoteButton.addEventListener('click', () => {
            score--;
            scoreElement.textContent = score;
        });
    });
});
