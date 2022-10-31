export default class Api {
  static new() {
    return new this();
  }

  /**
   * @private
   */
  constructor() {

  }

  async fetch() {
    const response = await (await fetch("http://127.3.3.3/Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/api.php"));
    if(response.ok) {
      const json = await response.json();
      return json.data
    } else {
      alert("HTTP-Error: " + response.status);
    }

  }
}