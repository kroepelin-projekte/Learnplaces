import FluxUiHttpRequestAsyncApi from './src/Adapters/Api/FluxUiHttpRequestAsyncApi.mjs';
export default class FluxUiHttpRequestApi {
  static new() {
    return new this();
  }

  /**
   * @private
   */
  constructor() {

  }

  fetch() {
      return FluxUiHttpRequestAsyncApi.new().fetch()
  }
}
const api = FluxUiHttpRequestApi.new();
const result = api.fetch();
console.log(result);
console.log("dddd");