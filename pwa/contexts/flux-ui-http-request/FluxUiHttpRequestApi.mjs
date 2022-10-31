import Api from './src/Adapters/Api/Api.mjs';
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
      return Api.new().fetch()
  }
}
const api = FluxUiHttpRequestApi.new();
const result = api.fetch();
console.log(result);
console.log("dddd");