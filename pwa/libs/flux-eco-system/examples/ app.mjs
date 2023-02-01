const ecoSystemApi = (await import("./../src/Adapters/Api/FluxEcoSystemApi.mjs")).FluxEcoSystemApi.new();
await ecoSystemApi.connectApplication("./flux-eco-ui-layout-orbital-definition.json");

