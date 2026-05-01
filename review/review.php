<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container py-4">

    <!-- STARS :) -->
    <div class="row justify-content-center mb-4">
        <?php include 'stars.php'; ?>
    </div>

    <div class="comment-section">
        <div class="mb-4">
            <div class="d-flex gap-3 align-items-start">
                <img src="profile.jpg" alt="User Avatar" class="user-avatar">
                <div class="flex-grow-1">
                    <textarea class="form-control comment-input" rows="3" placeholder="Write a comment..."></textarea>
                    <div class="mt-3 text-end">
                        <button class="btn btn-comment text-white">Post Comment</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- COMMENTS LIST -->
        <div class="comments-list">
            <div class="comment-box">
                <div class="d-flex gap-3">
                    <img src="profile.jpg" alt="User Avatar" class="user-avatar">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">John Doe</h6>
                            <span class="comment-time">2 hours ago</span>
                        </div>
                        <p class="mb-2">
                            This is an amazing post! Thanks for sharing your insights with us. I've
                            learned a lot from this.
                        </p>
                        <div class="comment-actions">
                            <a href="#"><i class="bi bi-heart"></i> Like</a>
                            <a href="#"><i class="bi bi-reply"></i> Reply</a>
                            <a href="#"><i class="bi bi-share"></i> Share</a>
                        </div>
                    </div>
                </div>

                <!-- REPLY SECTION -->
                <div class="reply-section mt-3">
                    <div class="comment-box">
                        <div class="d-flex gap-3">
                            <img src="profile.jpg" alt="User Avatar" class="user-avatar">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Jane Smith</h6>
                                    <span class="comment-time">1 hour ago</span>
                                </div>
                                <p class="mb-2">
                                    Totally agree with you! The points mentioned are very insightful.
                                </p>
                                <div class="comment-actions">
                                    <a href="#"><i class="bi bi-heart"></i> Like</a>
                                    <a href="#"><i class="bi bi-reply"></i> Reply</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="comment-box">
                <div class="d-flex gap-3">
                    <img src="profile.jpg" alt="User Avatar" class="user-avatar">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Mike Johnson</h6>
                            <span class="comment-time">3 hours ago</span>
                        </div>
                        <p class="mb-2">
                            Great discussion everyone! I'd like to add that this topic has many
                            interesting aspects we could explore further.
                        </p>
                        <div class="comment-actions">
                            <a href="#"><i class="bi bi-heart"></i> Like</a>
                            <a href="#"><i class="bi bi-reply"></i> Reply</a>
                            <a href="#"><i class="bi bi-share"></i> Share</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.star-rating:not(.readonly) label').forEach(star => {
    star.addEventListener('click', function() {
        this.style.transform = 'scale(1.2)';
        setTimeout(() => {
            this.style.transform = 'scale(1)';
        }, 200);
    });
});
</script>

</body>
</html>