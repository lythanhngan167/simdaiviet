// Tabs Action
const tabLink = document.querySelectorAll(".tab-menu-link");
const tabContent = document.querySelectorAll(".tab-bar-content");

tabLink.forEach((el) => {
  el.addEventListener("click", activeTab);
});

function activeTab(el) {
  const btnTarget = el.currentTarget;
  const content = btnTarget.dataset.content;

  tabContent.forEach((el) => {
    el.classList.remove("active");
  });

  tabLink.forEach((el) => {
    el.classList.remove("active");
  });

  document.querySelector("#" + content).classList.add("active");
  btnTarget.classList.add("active");
}