<?php
// 1. Start session and include database connection
session_start();
include 'db.php';

// 2. Check if user is logged in (needed for both the page and the AJAX handler)
 $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
 $userId = $isLoggedIn ? $_SESSION['user_id'] : 0;

?>
<?php
include 'header.php'?>

<!-- Playlist section -->
<section class="playlist-section spad">
<div class="container-fluid">
<div class="section-title">
<h2>Playlists</h2>
</div>

<div class="container">
<ul class="playlist-filter controls">
<?php
// Fetch genres from database
 $genreResult = mysqli_query($conn, "SELECT * FROM genre ORDER BY genre_name ASC");
if ($genreResult && mysqli_num_rows($genreResult) > 0):
    while ($genreRow = mysqli_fetch_assoc($genreResult)):
        // Convert genre name to lowercase and replace spaces with hyphens for CSS class
        $genreClass = strtolower(str_replace(' ', '-', $genreRow['genre_name']));
?>
<li class="control" data-filter=".<?= $genreClass ?>"><?= htmlspecialchars($genreRow['genre_name']) ?></li>
<?php
    endwhile;
endif;
?>
<li class="control" data-filter="all">All Playlist</li>
</ul>
</div>

<div class="clearfix"></div>
<div class="row playlist-area">
<?php
// ✅ Fetch videos from your table dynamically
 $sql = "
SELECT v.*, g.genre_name,
IFNULL(ROUND(AVG(r.rating_value),1), 0) AS avg_rating,
IFNULL(COUNT(r.rating_id), 0) AS rating_count
FROM video v
LEFT JOIN genre g ON v.genre_id = g.genre_id
LEFT JOIN rating r
ON r.content_type = 'video' AND r.content_id = v.video_id
WHERE v.is_new = 1
GROUP BY v.video_id
ORDER BY v.created_at DESC
";
 $result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0):
while ($row = mysqli_fetch_assoc($result)):

// Convert genre name to lowercase and replace spaces with hyphens for CSS class
 $genreClass = !empty($row['genre_name']) ? strtolower(str_replace(' ', '-', $row['genre_name'])) : 'genres';

// Star rating icons
 $rating = floatval($row['avg_rating']);
 $stars = '';
for ($i = 1; $i <= 5; $i++) {
 $stars .= ($i <= $rating)
? '<i class="fa fa-star text-warning"></i>'
: '<i class="fa fa-star-o text-muted"></i>';
}

// Get video file extension to determine format
 $videoPath = htmlspecialchars($row['file_path']);
 $videoExt = pathinfo($videoPath, PATHINFO_EXTENSION);
 $mimeType = '';

switch(strtolower($videoExt)) {
case 'mp4':
 $mimeType = 'video/mp4';
break;
case 'webm':
 $mimeType = 'video/webm';
break;
case 'ogg':
case 'ogv':
 $mimeType = 'video/ogg';
break;
default:
 $mimeType = 'video/mp4'; // Default to mp4
}

// Check if it's a YouTube URL
 $youtubeUrl = '';
 $embedUrl = '';
 $videoId = '';

if (strpos($videoPath, 'youtube.com') !== false || strpos($videoPath, 'youtu.be') !== false) {
// YouTube URL handling
if (strpos($videoPath, 'watch?v=') !== false) {
 $videoId = substr($videoPath, strpos($videoPath, 'v=') + 2);
if (strpos($videoId, '&') !== false) {
 $videoId = substr($videoId, 0, strpos($videoId, '&'));
}
 $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
} elseif (strpos($videoPath, 'youtu.be/') !== false) {
 $videoId = substr($videoPath, strpos($videoPath, 'be/') + 3);
if (strpos($videoId, '?') !== false) {
 $videoId = substr($videoId, 0, strpos($videoId, '?'));
}
 $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
}
} else {
// Local video file handling - database se uploaded video
 $embedUrl = 'uploads/' . $videoPath;
}
?>
<div class="mix col-lg-3 col-md-4 col-sm-6 <?= $genreClass ?> video-card"
data-video-id="<?= $row['video_id'] ?>"
data-video-src="<?= $embedUrl ?>"
data-video-type="<?= $mimeType ?>"
data-title="<?= htmlspecialchars($row['title']) ?>">
<div class="playlist-item shadow-sm p-2 rounded-3">
<!-- Video Thumbnail with Play Button -->
<div class="video-container position-relative">
<img src="<?= htmlspecialchars($row['thumbnail_img']) ?>"
alt="<?= htmlspecialchars($row['title']) ?>"
class="img-fluid rounded video-thumbnail">
<div class="play-button-overlay position-absolute top-50 start-50 translate-middle">
<button class="btn btn-danger rounded-circle play-video-btn"
data-video-src="<?= $embedUrl ?>"
data-video-type="<?= $mimeType ?>"
data-title="<?= htmlspecialchars($row['title']) ?>"
data-video-id="<?= $row['video_id'] ?>">
<i class="fa fa-play"></i>
</button>
</div>
<!-- Watch Link Button for YouTube -->
<?php if (!empty($youtubeUrl)): ?>
<div class="watch-button-overlay position-absolute top-50 start-50 translate-middle">
<button class="btn btn-primary rounded-circle watch-youtube-btn"
data-video-id="<?= $row['video_id'] ?>"
data-youtube-url="<?= $youtubeUrl ?>">
<i class="fa fa-youtube-play"></i>
</button>
</div>
<?php endif; ?>
</div>

<h5 class="mt-2 mb-1"><?= htmlspecialchars($row['title']) ?></h5>
<p class="small text-muted mb-1"><?= htmlspecialchars($row['description']) ?></p>

<!-- Display Genre -->
<div class="mb-2">
<span class="badge bg-secondary"><?= !empty($row['genre_name']) ? htmlspecialchars($row['genre_name']) : 'Unknown' ?></span>
</div>

<!-- Watch Now Link for YouTube -->
<?php if (!empty($youtubeUrl)): ?>
<div class="mb-2">
<button class="btn btn-sm btn-outline-primary watch-now-youtube-btn"
data-video-id="<?= $row['video_id'] ?>"
data-youtube-url="<?= $youtubeUrl ?>">
<i class="fa fa-youtube"></i> Watch
</button>
</div>
<?php endif; ?>

<!-- Dynamic Rating System -->
<div class="rating mb-2" data-video-id="<?= $row['video_id'] ?>">
<div class="stars-container">
<?php for ($i = 1; $i <= 5; $i++): ?>
<i class="fa fa-star star-rating
<?= ($i <= $rating) ? 'text-warning' : 'text-muted' ?>"
data-rating="<?= $i ?>"></i>
<?php endfor; ?>
</div>
<div class="rating-info mt-1">
<span class="rating-value"><?= $rating ?></span>/5
<span class="rating-count">(<?= $row['rating_count'] ?> votes)</span>
<span class="rating-message ms-2"></span>
</div>
</div>
</div>
</div>
<?php
endwhile;
else:
echo "<p class='text-center'>No playlists found.</p>";
endif;
?>
</div>
</div>
</section>
<!-- Playlist section end -->

<!-- Video Player Modal -->
<div class="modal" id="videoModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Video Title</h2>
            <button class="close-modal" id="closeVideoModal"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <!-- Close button above video -->
            <button class="close-video-btn" id="closeVideoBtn" title="Close Video">
                <i class="fas fa-times"></i>
            </button>
            <!-- Stop button for video -->
            <button class="stop-video-btn" id="stopVideoBtn" title="Stop Video">
                <i class="fas fa-stop"></i>
            </button>
            <div class="video-player">
                <!-- Video player for local videos -->
                <video id="modalVideoPlayer" controls class="w-100 d-none">
                    <source id="videoSource" src="" type="">
                    Your browser does not support the video tag.
                </video>
                <!-- Iframe for YouTube videos -->
                <iframe id="modalIframe" class="w-100 h-100 d-none" src="" allowfullscreen></iframe>
            </div>
        </div>
        <div class="modal-footer">
            <div class="rating-container">
                <div class="stars-container" id="modalStars"></div>
                <div class="rating-info">
                    <span class="rating-value" id="modalRatingValue">0</span>/5 
                    <span id="modalRatingCount">(0)</span>
                    <span class="rating-message" id="modalRatingMessage"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="toast" id="toast">
    <div class="toast-icon"><i class="fas fa-check-circle"></i></div>
    <div class="toast-message" id="toastMessage">Operation successful!</div>
</div>

<style>
/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    z-index: 1000;
    overflow: auto;
}

.modal.active {
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: #fff; /* Changed to white */
    color: #333; /* Changed text color to dark for readability */
    border-radius: 8px;
    width: 90%;
    max-width: 900px;
    position: relative;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
}

.modal-header {
    padding: 15px 20px;
    border-bottom: 1px solid #ddd; /* Changed border color for white background */
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 {
    margin: 0;
    font-size: 1.5rem;
}

.modal-body {
    padding: 0;
    position: relative;
}

.video-player {
    position: relative;
    width: 100%;
    height: 0;
    padding-bottom: 56.25%; /* 16:9 aspect ratio */
}

.video-player video,
.video-player iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: none;
}

.close-video-btn, .stop-video-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    z-index: 10;
    transition: background-color 0.3s;
}

.stop-video-btn {
    right: 60px;
}

.close-video-btn:hover, .stop-video-btn:hover {
    background-color: rgba(255, 0, 0, 0.7);
}

.modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #ddd; /* Changed border color for white background */
    display: flex;
    justify-content: center; /* Centered the rating */
    align-items: center;
}

.rating-container {
    display: flex;
    align-items: center;
}

.stars-container {
    margin-right: 10px;
}

.stars-container i {
    color: #ffc107;
    margin-right: 2px;
}

.rating-info {
    font-size: 0.9rem;
    color: #333; /* Changed text color for white background */
}

.close-modal {
    background: none;
    border: none;
    color: #333; /* Changed color for white background */
    font-size: 1.5rem;
    cursor: pointer;
}

.close-modal:hover {
    color: #e50914;
}

/* Toast Notification Styles */
.toast {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #333;
    color: #fff;
    padding: 15px 20px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    transform: translateY(100px);
    opacity: 0;
    transition: transform 0.3s, opacity 0.3s;
    z-index: 2000;
}

.toast.show {
    transform: translateY(0);
    opacity: 1;
}

.toast-icon {
    margin-right: 10px;
    color: #4CAF50;
}
</style>

<script>
// Check if user is logged in
const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;

// Video player functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle video card clicks - make the entire card clickable
    const videoCards = document.querySelectorAll('.video-card');
    
    videoCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Prevent triggering if clicking on rating stars or buttons
            if (e.target.closest('.rating') ||
                e.target.closest('.watch-youtube-btn') ||
                e.target.closest('.watch-now-youtube-btn')) {
                return;
            }
            
            const videoSrc = this.dataset.videoSrc;
            const videoType = this.dataset.videoType;
            const title = this.dataset.title;
            const videoId = this.dataset.videoId;
            const rating = this.querySelector('.rating-value').textContent;
            const ratingCount = this.querySelector('.rating-count').textContent;
            const stars = this.querySelector('.stars-container').innerHTML;
            
            // Set the title in modal
            document.getElementById('modalTitle').textContent = title;
            
            // Check if it's a YouTube video or local video
            const isYouTube = videoSrc.includes('youtube.com/embed');
            
            if (isYouTube) {
                // Show iframe for YouTube
                document.getElementById('modalIframe').src = videoSrc + '?autoplay=1';
                document.getElementById('modalIframe').classList.remove('d-none');
                document.getElementById('modalVideoPlayer').classList.add('d-none');
            } else {
                // Show video element for local videos
                document.getElementById('videoSource').src = videoSrc;
                document.getElementById('videoSource').type = videoType;
                document.getElementById('modalVideoPlayer').classList.remove('d-none');
                document.getElementById('modalIframe').classList.add('d-none');
                
                // Load and play the video
                const modalVideo = document.getElementById('modalVideoPlayer');
                modalVideo.load();
                modalVideo.play();
            }
            
            // Update rating information
            document.getElementById('modalStars').innerHTML = stars;
            document.getElementById('modalRatingValue').textContent = rating;
            document.getElementById('modalRatingCount').textContent = ratingCount;
            
            // Show the modal
            document.getElementById('videoModal').classList.add('active');
        });
    });

    // Handle modal close events
    document.getElementById('closeVideoModal').addEventListener('click', function() {
        document.getElementById('videoModal').classList.remove('active');
        stopVideo();
    });

    // Handle video stop button
    document.getElementById('stopVideoBtn').addEventListener('click', function() {
        stopVideo();
    });

    // Handle close video button
    document.getElementById('closeVideoBtn').addEventListener('click', function() {
        document.getElementById('videoModal').classList.remove('active');
        stopVideo();
    });

    // Function to stop video playback
    function stopVideo() {
        // Stop local video playback
        const modalVideo = document.getElementById('modalVideoPlayer');
        modalVideo.pause();
        modalVideo.currentTime = 0;

        // Clear YouTube iframe src to stop playback
        document.getElementById('modalIframe').src = '';
    }

    // Handle YouTube watch button clicks
    const youtubeButtons = document.querySelectorAll('.watch-youtube-btn, .watch-now-youtube-btn');

    youtubeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent triggering the card click event
            const videoId = this.dataset.videoId;
            const youtubeUrl = this.dataset.youtubeUrl;

            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Loading...';
            this.disabled = true;

            // Send AJAX request to update database
            fetch('update_watch_count.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `video_id=${videoId}`
            })
            .then(response => response.json())
            .then(data => {
                // Open YouTube in new tab
                window.open(youtubeUrl, '_blank');

                // Restore button state
                this.innerHTML = originalText;
                this.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);

                // Still open YouTube even if update fails
                window.open(youtubeUrl, '_blank');

                // Restore button state
                this.innerHTML = originalText;
                this.disabled = false;
            });
        });
    });

    // Handle star rating clicks
    const starRatings = document.querySelectorAll('.star-rating');

    starRatings.forEach(star => {
        star.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent triggering the card click event

            // Check if user is logged in
            if (!isLoggedIn) {
                alert('Please log in to rate videos');
                return;
            }

            const videoId = this.closest('.rating').dataset.videoId;
            const rating = this.dataset.rating;

            // Send AJAX request to save rating
            fetch('save_rating.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `video_id=${videoId}&rating_value=${rating}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the display
                    const ratingContainer = this.closest('.rating');
                    const stars = ratingContainer.querySelectorAll('.star-rating');
                    const ratingValue = ratingContainer.querySelector('.rating-value');
                    const ratingCount = ratingContainer.querySelector('.rating-count');
                    const ratingMessage = ratingContainer.querySelector('.rating-message');

                    // Update stars
                    stars.forEach((star, index) => {
                        if (index < data.new_rating) {
                            star.classList.remove('fa-star-o', 'text-muted');
                            star.classList.add('fa-star', 'text-warning');
                        } else {
                            star.classList.remove('fa-star', 'text-warning');
                            star.classList.add('fa-star-o', 'text-muted');
                        }
                    });

                    // Update rating value and count
                    ratingValue.textContent = data.new_rating.toFixed(1);
                    ratingCount.textContent = `(${data.rating_count} votes)`;

                    // Show success message
                    ratingMessage.textContent = 'Thanks for rating!';
                    ratingMessage.classList.add('text-success');

                    // Hide message after 3 seconds
                    setTimeout(() => {
                        ratingMessage.textContent = '';
                        ratingMessage.classList.remove('text-success');
                    }, 3000);
                } else {
                    // Show error message
                    const ratingMessage = this.closest('.rating').querySelector('.rating-message');
                    ratingMessage.textContent = data.message || 'Error saving rating';
                    ratingMessage.classList.add('text-danger');

                    // Hide message after 3 seconds
                    setTimeout(() => {
                        ratingMessage.textContent = '';
                        ratingMessage.classList.remove('text-danger');
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const ratingMessage = this.closest('.rating').querySelector('.rating-message');
                ratingMessage.textContent = 'Error saving rating';
                ratingMessage.classList.add('text-danger');

                // Hide message after 3 seconds
                setTimeout(() => {
                    ratingMessage.textContent = '';
                    ratingMessage.classList.remove('text-danger');
                }, 3000);
            });
        });

        // Add hover effect
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            const stars = this.closest('.stars-container').querySelectorAll('.star-rating');

            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('text-muted');
                    star.classList.add('text-warning');
                } else {
                    star.classList.remove('text-warning');
                    star.classList.add('text-muted');
                }
            });
        });
    });

    // Reset to original rating when mouse leaves the stars container
    document.querySelectorAll('.stars-container').forEach(container => {
        container.addEventListener('mouseleave', function() {
            const ratingContainer = this.closest('.rating');
            const ratingValue = parseFloat(ratingContainer.querySelector('.rating-value').textContent);
            const stars = this.querySelectorAll('.star-rating');

            stars.forEach((star, index) => {
                if (index < ratingValue) {
                    star.classList.remove('fa-star-o', 'text-muted');
                    star.classList.add('fa-star', 'text-warning');
                } else {
                    star.classList.remove('fa-star', 'text-warning');
                    star.classList.add('fa-star-o', 'text-muted');
                }
            });
        });
    });

    // Toast notification function
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        const toastIcon = toast.querySelector('.toast-icon i');
        
        toastMessage.textContent = message;
        
        if (type === 'error') {
            toastIcon.className = 'fas fa-exclamation-circle';
            toastIcon.style.color = '#f44336';
        } else {
            toastIcon.className = 'fas fa-check-circle';
            toastIcon.style.color = '#4CAF50';
        }
        
        toast.classList.add('show');
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === document.getElementById('videoModal')) {
            document.getElementById('videoModal').classList.remove('active');
            stopVideo();
        }
    });

    // Close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (document.getElementById('videoModal').classList.contains('active')) {
                document.getElementById('videoModal').classList.remove('active');
                stopVideo();
            }
        }
    });
});
</script>

<?php
include 'footer.php';
?>