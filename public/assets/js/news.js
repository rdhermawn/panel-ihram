let cardList = document.getElementById("post-list");
let cardNews = cardList.querySelectorAll(".card-news");
let noPost = document.getElementById("no-post");

const selectCategory = document.getElementById("category");
selectCategory.addEventListener("change", (e) => {
  postFilter();
});
const selectLimit = document.getElementById("limit");
selectLimit.addEventListener("change", (e) => {
  postFilter();
});

selectCategory.value = "all";
selectLimit.value = 10;

function postFilter() {
  let category = selectCategory.value;
  let totalCategory = 0;
  let limit = 0;

  cardNews.forEach((card) => {
    if (card.dataset.category === category || category === "all") {
      totalCategory++;
    }
  });

  if (selectLimit.value === "all") {
    limit = totalCategory;
  } else {
    limit =
      selectLimit.value > totalCategory ? totalCategory : selectLimit.value;
  }

  if (totalCategory === 0) {
    noPost.style.display = "block";
  } else {
    noPost.style.display = "none";
  }

  if (category === "all") {
    cardNews.forEach((card) => {
      card.style.display = "none";
    });
    for (let i = 0; i < limit; i++) {
      cardNews[i].style.display = "flex";
    }
  } else {
    cardNews.forEach((card) => {
      card.style.display = "none";
    });
    for (let i = 0; i <= limit; i++) {
      if (cardNews[i].dataset.category == category) {
        cardNews[i].style.display = "flex";
      }
    }
  }
}
