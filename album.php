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

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<!-- Floating Music Notes -->
<div class="music-notes" id="musicNotes"></div>

<div class="container">
    <h1>Albums</h1>
    
    <div class="album-thumbnails">
    <?php
    $albumQuery = "SELECT * FROM album";
    $albumResult = mysqli_query($conn, $albumQuery);

    if (mysqli_num_rows($albumResult) > 0) {
        while ($album = mysqli_fetch_assoc($albumResult)) {
            $album_id = $album['album_id'];
            $album_name = $album['album_name'];
            $description = $album['description'];
            $cover_image = $album['cover_image'];

            // Fetch 3 songs
            $musicQuery = "SELECT * FROM music WHERE album_id = '$album_id' LIMIT 3";
            $musicResult = mysqli_query($conn, $musicQuery);
            $songs = [];
            while ($m = mysqli_fetch_assoc($musicResult)) {
                $songs[] = $m['file_path'];
            }

            // Fetch 4 videos with additional details
            $videoQuery = "SELECT * FROM video WHERE album_id = '$album_id' LIMIT 4";
            $videoResult = mysqli_query($conn, $videoQuery);
            $videos = [];
            while ($v = mysqli_fetch_assoc($videoResult)) {
                // Store both file path and video title
                $videos[] = [
                    'file_path' => $v['file_path'],
                    'title' => $v['title'],
                    'is_youtube' => (strpos($v['file_path'], 'youtube.com') !== false || strpos($v['file_path'], 'youtu.be') !== false)
                ];
            }

            echo '
            <div class="album-card" 
                data-songs="'.htmlspecialchars(json_encode($songs)).'" 
                data-videos="'.htmlspecialchars(json_encode($videos)).'" 
                data-title="'.htmlspecialchars($album_name).'" 
                data-description="'.htmlspecialchars($description).'" 
                data-cover="'.$cover_image.'">
                <div class="play-overlay">
                    <i class="fas fa-play"></i>
                </div>
                <img src="'.$cover_image.'" alt="'.htmlspecialchars($album_name).'">
                <div class="album-info">
                    <h3>'.htmlspecialchars($album_name).'</h3>
                    <p>'.htmlspecialchars($description).'</p>
                    <div class="meta">
                        <span><i class="fas fa-music"></i> '.count($songs).' Songs</span>
                        <span><i class="fas fa-video"></i> '.count($videos).' Videos</span>
                    </div>
                </div>
            </div>';
        }
    } else {
        echo "<p>No albums found.</p>";
    }
    ?>
    </div>
</div>

<!-- Modal -->
<div id="albumModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <div class="modal-header">
            <h2 id="modal-title">Album Title</h2>
        </div>
        <div class="modal-images">
            <img id="modal-img-1" src="" alt="Album Cover">
        </div>
        <div class="media-player">
            <h3>🎵 Related Songs</h3>
            <div class="songs-list" id="songs-list"></div>
        </div>
        <div class="media-player">
            <h3>🎬 Music Videos</h3>
            <div class="videos-list" id="videos-list"></div>
        </div>
        <div class="modal-description">
            <h3>ℹ️ Album Information</h3>
            <p id="modal-description"></p>
        </div>
    </div>
</div>

<!-- Scroll to Top Button -->
<div class="scroll-top" id="scrollTop">
    <i class="fas fa-arrow-up"></i>
</div>

<style>
/* Video container styling */
.video-container {
    position: relative;
    width: 100%;
    margin-bottom: 15px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.video-container video,
.video-container iframe {
    width: 100%;
    height: 250px;
    object-fit: cover;
    display: block;
}

.video-title {
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 8px 12px;
    font-size: 14px;
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    margin: 0;
}
</style>

<script>
// Loading Animation
window.addEventListener('load', function() {
    setTimeout(function() {
        document.getElementById('loadingOverlay').style.opacity = '0';
        setTimeout(function() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }, 500);
    }, 1000);
});

// Generate Floating Music Notes
const musicNotes = document.getElementById('musicNotes');
const notes = ['♪', '♫', '♬', '♩', '♭', '♮', '♯'];
for (let i = 0; i < 15; i++) {
    const note = document.createElement('div');
    note.className = 'music-note';
    note.textContent = notes[Math.floor(Math.random() * notes.length)];
    note.style.left = Math.random() * 100 + '%';
    note.style.animationDelay = Math.random() * 15 + 's';
    note.style.fontSize = (Math.random() * 20 + 10) + 'px';
    musicNotes.appendChild(note);
}

// Scroll to Top Button
const scrollTopBtn = document.getElementById('scrollTop');
window.addEventListener('scroll', function() {
    if (window.pageYOffset > 300) {
        scrollTopBtn.classList.add('active');
    } else {
        scrollTopBtn.classList.remove('active');
    }
});

scrollTopBtn.addEventListener('click', function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

const modal = document.getElementById('albumModal');
const modalTitle = document.getElementById('modal-title');
const modalDescription = document.getElementById('modal-description');
const modalImg1 = document.getElementById('modal-img-1');
const songsList = document.getElementById('songs-list');
const videosList = document.getElementById('videos-list');
const closeBtn = document.querySelector('.close-btn');

document.querySelectorAll('.album-card').forEach(card => {
    card.addEventListener('click', () => {
        modal.style.display = 'block';
        modalTitle.textContent = card.dataset.title;
        modalDescription.textContent = card.dataset.description;
        modalImg1.src = card.dataset.cover;

        // 3 Songs
        songsList.innerHTML = "";
        let songs = JSON.parse(card.dataset.songs || "[]");
        if (songs.length === 0) {
            songsList.innerHTML = "<p>No songs available.</p>";
        } else {
            songs.forEach(song => {
                let audio = document.createElement('audio');
                audio.src = song;
                audio.controls = true;
                songsList.appendChild(audio);
            });
        }

        // 4 Videos
        videosList.innerHTML = "";
        let videos = JSON.parse(card.dataset.videos || "[]");
        if (videos.length === 0) {
            videosList.innerHTML = "<p>No videos available.</p>";
        } else {
            videos.forEach(videoData => {
                // Check if it's a YouTube video or local video
                if (videoData.is_youtube) {
                    // Handle YouTube video
                    let videoURL = videoData.file_path;
                    if (videoURL.includes("watch?v=")) {
                        videoURL = videoURL.replace("watch?v=", "embed/");
                    } else if (videoURL.includes("youtu.be/")) {
                        videoURL = videoURL.replace("youtu.be/", "www.youtube.com/embed/");
                    }
                    
                    let container = document.createElement('div');
                    container.className = 'video-container';
                    
                    let iframe = document.createElement('iframe');
                    iframe.src = videoURL;
                    iframe.allowFullscreen = true;
                    iframe.setAttribute('frameborder', '0');
                    
                    let title = document.createElement('p');
                    title.className = 'video-title';
                    title.textContent = videoData.title;
                    
                    container.appendChild(iframe);
                    container.appendChild(title);
                    videosList.appendChild(container);
                } else {
                    // Handle local video from uploads folder
                    let container = document.createElement('div');
                    container.className = 'video-container';
                    
                    let video = document.createElement('video');
                    video.src = 'uploads/' + videoData.file_path;
                    video.controls = true;
                    
                    let title = document.createElement('p');
                    title.className = 'video-title';
                    title.textContent = videoData.title;
                    
                    container.appendChild(video);
                    container.appendChild(title);
                    videosList.appendChild(container);
                }
            });
        }
    });
});

closeBtn.onclick = () => {
    modal.style.display = 'none';
    videosList.innerHTML = "";
};

window.onclick = (event) => {
    if (event.target === modal) {
        modal.style.display = 'none';
        videosList.innerHTML = "";
    }
};
</script>

<?php include 'footer.php'; ?>

</body>
</html>