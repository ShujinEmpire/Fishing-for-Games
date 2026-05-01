<div class="col-md-6">
    <div class="rating-card p-4">
        <h5 class="mb-4">Interactive Star Rating</h5>

        <div class="star-rating animated-stars">
            <input type="radio" id="star5" name="rating" value="5" <?php echo (($selectedRating ?? 0) == 5) ? 'checked' : ''; ?>>
            <label for="star5" class="bi bi-star-fill"></label>

            <input type="radio" id="star4" name="rating" value="4" <?php echo (($selectedRating ?? 0) == 4) ? 'checked' : ''; ?>>
            <label for="star4" class="bi bi-star-fill"></label>

            <input type="radio" id="star3" name="rating" value="3" <?php echo (($selectedRating ?? 0) == 3) ? 'checked' : ''; ?>>
            <label for="star3" class="bi bi-star-fill"></label>

            <input type="radio" id="star2" name="rating" value="2" <?php echo (($selectedRating ?? 0) == 2) ? 'checked' : ''; ?>>
            <label for="star2" class="bi bi-star-fill"></label>

            <input type="radio" id="star1" name="rating" value="1" <?php echo (($selectedRating ?? 0) == 1) ? 'checked' : ''; ?>>
            <label for="star1" class="bi bi-star-fill"></label>
        </div>

        <p class="text-muted mt-2">Click to rate</p>
    </div>
</div>