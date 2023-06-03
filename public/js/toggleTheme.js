function toggleTheme() {
  const theme = document.documentElement.getAttribute("data-theme");
  if (theme === "dark") {
    document.documentElement.setAttribute("data-theme", "light");
    document.cookie = "theme=light; path=/; max-age=3600"; // set the cookie for 1 hour
  } else {
    document.documentElement.setAttribute("data-theme", "dark");
    document.cookie = "theme=dark; path=/; max-age=3600";
  }
}

// check for the theme cookie on page load and set the theme accordingly
window.onload = function () {
  const theme = getCookie("theme");
  if (theme) {
    document.documentElement.setAttribute("data-theme", theme);
  }
};

// helper function to get a cookie by name
function getCookie(name) {
  const value = "; " + document.cookie;
  const parts = value.split("; " + name + "=");
  if (parts.length === 2) {
    return parts.pop().split(";").shift();
  }
}
