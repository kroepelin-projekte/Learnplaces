export default class Slot {
    #name;
    #onEvent;

    static new(name, onEvent) {
        return new this(name, onEvent);
    }

    constructor(name, onEvent) {
      this.#name = name;
      this.#onEvent = onEvent;
    }

    replaceSlotData(payload, replyTo) {
        this.#onEvent(replyTo, {})
    }
}