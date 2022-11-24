import FluxLayoutApi from "../../src/Adapters/Api/FluxLayoutApi.mjs";
const applicationName = "example-app"
const definitionsBaseUrl = window.location + "../../definitions";

await FluxLayoutApi.initialize(
  {
    applicationName: applicationName,
    logEnabled: true,
    definitionsBaseUrl: definitionsBaseUrl,
  },
);