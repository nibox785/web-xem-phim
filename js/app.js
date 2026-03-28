document.addEventListener("DOMContentLoaded", () => {
  const arrows = document.querySelectorAll(".arrow");
  const movieLists = document.querySelectorAll(".movie-list");

  arrows.forEach((arrow, i) => {
    if (!movieLists[i]) {
      console.error(`Movie list at index ${i} not found`);
      return;
    }

    const itemNumber = movieLists[i].querySelectorAll("img").length;
    if (itemNumber === 0) {
      console.warn(`No images found in movie list at index ${i}`);
      return;
    }

    let clickCounter = 0;
    arrow.addEventListener("click", () => {
      const ratio = Math.max(1, Math.floor(window.innerWidth / 270));
      clickCounter++;

      const maxItemsVisible = 4;
      if (clickCounter < itemNumber - maxItemsVisible) {
        const transformValue = window.getComputedStyle(movieLists[i]).transform;
        let xValue = 0;
        if (transformValue !== "none") {
          const matrix = transformValue.match(/matrix.*\((.+)\)/);
          if (matrix) {
            const values = matrix[1].split(", ");
            xValue = parseFloat(values[4]);
          }
        }
        movieLists[i].style.transform = `translateX(${xValue - 300}px)`;
      } else {
        movieLists[i].style.transform = "translateX(0)";
        clickCounter = 0;
      }

      console.log(
        "Ratio:",
        ratio,
        "Item Number:",
        itemNumber,
        "Click Counter:",
        clickCounter
      );
    });
  });
});
