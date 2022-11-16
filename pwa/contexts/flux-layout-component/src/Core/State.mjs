export default class State {

  /**
   * @var array
   */
  #attributes = [];

  static new() {
      return new this();
  }

  constructor() {

  }

  change(attributeName, attribute, onChange) {

      if(this.#attributes.includes(attributeName) && this.#attributes[attributeName] !== attribute) {
        this.#attributes[attributeName] = attribute;
        onChange(attributeName, attribute);
        return;
      }

    if(this.#attributes.includes(attributeName) === false) {
      this.#attributes[attributeName] = attribute;
      onChange(attributeName, attribute);
    }
  }
}