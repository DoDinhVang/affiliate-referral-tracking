// import "./bootstrap";
// import createApp from "@shopify/app-bridge";
// import { getSessionToken } from "@shopify/app-bridge/utilities";

// console.log("host", new URLSearchParams(location.search).get("host"));
// console.log(
//     "VITE_SHOPIFY_CLIENT_SECRET:",
//     import.meta.env.VITE_SHOPIFY_CLIENT_SECRET,
// );

// const app = createApp({
//     apiKey: import.meta.env.VITE_SHOPIFY_CLIENT_SECRET,
//     host: new URLSearchParams(location.search).get("host"),
// });

// console.log("App created:", app);
// console.log("running init function");

// async function init() {
//     try {
//         console.log("Starting init function...");
//         const sessionToken = await getSessionToken(app);
//         console.log("my-session-token", sessionToken);
//     } catch (error) {
//         console.error("Error in init function:", error);
//     }
// }

// init();
