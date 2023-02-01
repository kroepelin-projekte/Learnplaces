export class MessageRecorder {
    /**
     * @var {Map}
     */
    recordedMessages;

    constructor() {
        this.recordedMessages = new Map();
    }

    static new() {
        return new this();
    }

    /**
     * @param {DomainMessageName} messageName
     * @param {Object} payload
     */
    record(messageName, payload) {
        this.recordedMessages.set(messageName, payload);
    }

    flush() {
        this.recordedMessages = new Map();
    }
}