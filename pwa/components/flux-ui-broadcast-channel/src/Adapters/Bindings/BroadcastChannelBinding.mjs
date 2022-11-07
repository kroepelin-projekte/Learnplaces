export default class BroadcastChannelBinding {

  tagName;

  static new(tagName) {
    return new this(tagName);
  }

  constructor(tagName) {
    this.tagName = tagName;
  }

  addListener(messageName, onMessage) {
    const channel = new BroadcastChannel(this.tagName + "/" + messageName)

    console.log('addEvent Listender '  + this.tagName + "/" + messageName);

    channel.addEventListener('message', message => {
      onMessage(message);
    })
  }

  publish(message) {
    const messageName = message.headers.name
    const channel = new BroadcastChannel(this.tagName + "/" + messageName)

    console.log(messageName)

    channel.postMessage(
      message
    )
  }

  /**
   * @param logEnabled
   * @return {(function(*): void)|*}
   */
  publishCallback(logEnabled = false) {

    const tagName = this.tagName;
    const publishIt = (message) => {
      this.publish(message);
    }

    return (message) => {
      if (logEnabled === true) {
        const name = message.headers.name;
        const payload = message.payload;
        console.log({ tagName, name, payload });
      }
      publishIt(message)
    }
/*
    const channelId = this.channelId;
    const publish = (message) => {
      this.publish(message);
    }
    const addListener = (message) => this.addListener(message.headers.name, (message) => {
      //  const name = message.headers.name;
      const payload = message.payload
      console.log({ channelId, name, payload });
    });

    return function (message) {
      if (logEnabled === true) {
        try {
          addListener(message);
        }
        catch (e) {
          console.log({ channelId, e });
        }
      }
      publish(message)
    }*/
  }
}