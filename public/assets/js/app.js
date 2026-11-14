// Get content from class menu to id menu
const menu = document.querySelector(".menu-nav");
const menuList = document.getElementById("menu-mobile");
menuList.innerHTML = menu.innerHTML;
// Menu Mobile
const openMenu = document.getElementById("open-menu");
const closeMenu = document.getElementById("close-menu");
const menuMobile = document.querySelector(".menu-mobile");
// Open Menu
openMenu.addEventListener("click", () => {
  menuMobile.classList.add("flex");
  menuMobile.classList.remove("hidden");
});
// Close Menu
closeMenu.addEventListener("click", () => {
  menuMobile.classList.add("hidden");
});

// sidemenu
const checkSidemenu = document.getElementById("check-sidemenu");
const sidemenu = document.getElementById("sidemenu");

// default sidemenu
if (checkSidemenu && checkSidemenu.checked) {
  sidemenu.classList.add("hidden");
}

if (checkSidemenu) {
  checkSidemenu.addEventListener("click", () => {
    if (checkSidemenu.checked) {
      sidemenu.classList.add("hidden");
    } else {
      sidemenu.classList.remove("hidden");
    }
  });
}
