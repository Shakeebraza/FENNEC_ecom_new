
document
  .getElementById("toggleCategories")
  .addEventListener("click", function (e) {
    e.preventDefault();
    const moreCategories = document.getElementById("moreCategories");
    const toggleLink = document.getElementById("toggleCategories");

    if (moreCategories.style.display === "none") {
      moreCategories.style.display = "block";
      toggleLink.textContent = "Show less";
    } else {
      moreCategories.style.display = "none";
      toggleLink.textContent = "Show 30 more";
    }
  });

document
  .getElementById("toggleContractTypes")
  .addEventListener("click", function (e) {
    e.preventDefault();
    const moreContractTypes = document.getElementById("moreContractTypes");
    const toggleLink = document.getElementById("toggleContractTypes");

    if (moreContractTypes.style.display === "none") {
      moreContractTypes.style.display = "block";
      toggleLink.textContent = "Show less";
    } else {
      moreContractTypes.style.display = "none";
      toggleLink.textContent = "Show 2 more";
    }
  });
document
  .getElementById("toggleRecruiterTypes")
  .addEventListener("click", function (e) {
    e.preventDefault();
    const moreRecruiterTypes = document.getElementById("moreRecruiterTypes");
    const toggleLink = document.getElementById("toggleRecruiterTypes");

    if (moreRecruiterTypes.style.display === "none") {
      moreRecruiterTypes.style.display = "block";
      toggleLink.textContent = "Show less";
    } else {
      moreRecruiterTypes.style.display = "none";
      toggleLink.textContent = "Show more";
    }
  });

document
  .getElementById("toggleJobTypes")
  .addEventListener("click", function (e) {
    e.preventDefault();
    const moreJobTypes = document.getElementById("moreJobTypes");
    const toggleLink = document.getElementById("toggleJobTypes");

    if (moreJobTypes.style.display === "none") {
      moreJobTypes.style.display = "block";
      toggleLink.textContent = "Show less";
    } else {
      moreJobTypes.style.display = "none";
      toggleLink.textContent = "Show more";
    }
  });

document
  .getElementById("toggleLanguages")
  .addEventListener("click", function (e) {
    e.preventDefault();
    const moreLanguages = document.getElementById("moreLanguages");
    const toggleLink = document.getElementById("toggleLanguages");

    if (moreLanguages.style.display === "none") {
      moreLanguages.style.display = "block";
      toggleLink.textContent = "Show less";
    } else {
      moreLanguages.style.display = "none";
      toggleLink.textContent = "Show more";
    }
  });
