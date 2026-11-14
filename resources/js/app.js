// Keyboard shortcut for the search box in the header: Ctrl/Cmd+K or "/".
// (Alpine is bundled by Livewire, so it isn't imported here.)
document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-input");
  const searchButton = document.getElementById("search-button");

  if (!searchInput) {
    return;
  }

  searchButton?.addEventListener("click", () => searchInput.focus());

  document.addEventListener("keydown", (event) => {
    const isCtrlK = (event.metaKey || event.ctrlKey) && event.key === "k";
    const isSlash =
      event.key === "/" &&
      !["INPUT", "TEXTAREA", "SELECT"].includes(document.activeElement?.tagName);

    if (isCtrlK || isSlash) {
      event.preventDefault();
      searchInput.focus();
    }
  });
});
