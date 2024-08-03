document.querySelectorAll('.like-btn').forEach(button => {
    button.addEventListener('click', function() {
        const type = this.dataset.type; // 'thread' or 'comment'
        const id = this.dataset.id;

        fetch('like.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `thread_id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'redirect') {
                alert(data.message);
                window.location.href = 'index.php?alert=' + encodeURIComponent(data.message);
            } else if (data.status === 'success') {
                const likeCountElement = document.getElementById(`like-count-${id}`);
                likeCountElement.textContent = data.likes;
                if (data.action === 'like') {
                    this.querySelector('i').classList.remove('bi-hand-thumbs-up');
                    this.querySelector('i').classList.add('bi-hand-thumbs-up-fill');
                } else {
                    this.querySelector('i').classList.remove('bi-hand-thumbs-up-fill');
                    this.querySelector('i').classList.add('bi-hand-thumbs-up');
                }
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
