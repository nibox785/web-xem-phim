let player;

function onYouTubeIframeAPIReady() {
  const playerElement = document.getElementById("youtube-player");
  if (playerElement) {
    player = new YT.Player("youtube-player", {
      events: {
        onReady: onPlayerReady,
        onError: onPlayerError,
        onStateChange: onPlayerStateChange,
      },
    });
  } else {
    console.error("YouTube player element not found");
  }
}

function onPlayerReady(event) {
  const videoWrapper = document.getElementById("video-wrapper");
  if (videoWrapper) {
    videoWrapper.classList.add("loaded");
  }
}

function onPlayerStateChange(event) {
  if (event.data === YT.PlayerState.PLAYING) {
    const videoWrapper = document.getElementById("video-wrapper");
    if (videoWrapper) {
      videoWrapper.classList.add("loaded");
    }
  }
}

function onPlayerError(event) {
  console.error("YouTube Player Error:", event.data);
  const videoWrapper = document.getElementById("video-wrapper");
  if (videoWrapper) {
    videoWrapper.innerHTML =
      '<p style="color: red; text-align: center; padding: 20px;">Lỗi khi tải video. Mã lỗi: ' +
      event.data +
      "</p>";
  }
}

function seekVideo(seconds) {
  if (player) {
    player.seekTo(player.getCurrentTime() + seconds);
  }
}

function togglePlay() {
  if (player) {
    if (player.getPlayerState() === 1) {
      player.pauseVideo();
    } else {
      player.playVideo();
    }
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const ratingStars = document.querySelectorAll(".rating-star");
  ratingStars.forEach((star) => {
    star.addEventListener("click", function () {
      const value = this.getAttribute("data-value");
      const ratingValueInput = document.getElementById("rating-value");
      if (ratingValueInput) {
        ratingValueInput.value = value;
        ratingStars.forEach((s) => {
          s.style.color =
            s.getAttribute("data-value") <= value ? "#ff9b39" : "#ccc";
        });
      }
    });
  });
});
