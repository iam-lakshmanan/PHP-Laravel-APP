(function () {

    const AppConstants = {
  INTRANET_DOMAIN: 'intranet.procept.com',
   PUBLIC_DOMAIN: 'https://procept.com'
};


  function replaceImgUrl(img) {
    if (
      img.tagName === 'IMG' &&
      img.src &&
      img.src.includes(AppConstants.INTRANET_DOMAIN) &&
      !img.dataset.processed
    ) {
      const relativePath = img.src.split(AppConstants.INTRANET_DOMAIN)[1];
      if (!relativePath) return;

      let newSrc;

        newSrc = `${AppConstants.PUBLIC_DOMAIN}${relativePath}`;


      img.dataset.processed = "true";
      img.src = newSrc;
    }
  }

  function processAllImages() {
    document.querySelectorAll("img").forEach(replaceImgUrl);
  }

  window.addEventListener("load", function () {
    processAllImages();

    // Fallback retry for late-loaded images
    setTimeout(processAllImages, 1000);
  });

  const observer = new MutationObserver(mutations => {
    mutations.forEach(mutation => {
      mutation.addedNodes.forEach(node => {
        if (node.tagName === 'IMG') {
          replaceImgUrl(node);
        } else if (node.querySelectorAll) {
          node.querySelectorAll('img').forEach(replaceImgUrl);
        }
      });
    });
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true
  });
})();
