window.addEventListener("DOMContentLoaded", () => {
  const back = document.getElementById("back");
  if(back != null) {
    back.addEventListener("click", (event) => {
      event.preventDefault();
      window.history.go(-1);
    });
  }
});
