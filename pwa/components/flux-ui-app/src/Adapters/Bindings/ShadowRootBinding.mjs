export default class ShadowRootBinding {

  channelId;

  static new(channelId) {
    return new this(channelId);
  }

  constructor(channelId) {
    this.channelId = channelId;
  }

  addListener(messageName, onMessage) {
    const channel = new BroadcastChannel(this.channelId + "/" + messageName)
    channel.addEventListener('message', message => {
      onMessage(message);
    })
  }

}