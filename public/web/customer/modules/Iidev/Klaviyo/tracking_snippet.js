// helpers

async function klaviyoLoadCheck() {
  let isLoaded = false;
  let attempts = 0;
  const maxAttempts = 5;

  const checkInterval = setInterval(() => {
    attempts++;

    if (typeof klaviyo === "object" && klaviyo !== null) {
      isLoaded = true;
      clearInterval(checkInterval);
    }

    if (attempts >= maxAttempts) {
      clearInterval(checkInterval);
    }
  }, 1000);

  while (attempts <= maxAttempts && !isLoaded) {
    await new Promise((resolve) => setTimeout(resolve, 1000));
  }
  return isLoaded;
}

// triggers

xcart.bind("klaviyoAddedToCart", async (_, data) => {
  const klaviyoLoaded = await klaviyoLoadCheck();

  if (!klaviyoLoaded) return;

  klaviyo.push(["track", "Added to Cart", data]);
});

if (window.klaviyoLayer) {
  (async () => {
    const klaviyoLoaded = await klaviyoLoadCheck();

    if (!klaviyoLoaded) return;

    switch (window.klaviyoLayer.type) {
      case "identify":
        klaviyo.push([
          "identify",
          {
            email: window.klaviyoLayer?.data,
          },
        ]);

        break;
      case "product":
        klaviyo.push([
          "track",
          "Viewed Product",
          window.klaviyoLayer?.data?.viewedProduct,
        ]);
        klaviyo.push([
          "track",
          "trackViewedItem",
          window.klaviyoLayer?.data?.trackViewedItem,
        ]);

        break;
      case "checkout":
        klaviyo.push(["track", "Started Checkout", window.klaviyoLayer?.data]);

        break;
      default:
        break;
    }
  })();
}
