import Actor from '../../Core/Actor.mjs';
import MessageStream from '../MessageStream/MessageStream.mjs';
import Definitions from '../Definitions/Definitions.mjs';

export default class FluxAppApi {
  /** @var {string} */
  #id;
  /** @var {Actor} */
  #actor;
  /** @var {MessageStream} */
  #messageStream;
  /** @var {Definitions} */
  #definitions;

  /**
   * @private
   */
  constructor(applicationName) {
    this.#id = applicationName;
  }

  /**
   * @return {FluxAppApi}
   */
  static async initialize(applicationId, logEnabled) {
    const obj = new FluxAppApi(
      applicationId
    );
    obj.#messageStream = await MessageStream.new(obj.#id, logEnabled);
    await obj.#initDefinitions();
    await obj.#initReactors();
    await obj.#initActor()
  }

  async #initDefinitions() {
    this.#definitions = await Definitions.new(await document.getElementById("flux-pwa-base").href)
  }

  async #initActor() {
    this.#actor = await Actor.new(this.#id, (publishAddress, payload) => {
        this.#publish(
          publishAddress,
          payload
        )
      }
    );
  }

  async #initReactors() {
    const apiDefinition = await this.#definitions.apiDefinition();
    Object.entries(apiDefinition.reactions).forEach(([reactionId, reaction]) => {
      const addressDef = reaction.onMessage;
      const address = addressDef.replace('{$applicationId}', this.#id);
      this.#messageStream.register(address, (messagePayload) => {

        let messagePayloadData = null;
        if(messagePayload.data) {
          messagePayloadData = {data: messagePayload.data}
        }

        const payload = {
          ...reaction.payload.commandPayload,
          ...messagePayloadData
        }
        console.log(payload);

        this.#react(reaction.process,reaction.payload.command, payload)
      })
    });
  }

  async #react(process, command, payload) {
    try {
      this.#actor[process](command, payload);
    }
    catch (e) {
      console.error(process + " " + e)
    }
  }


  async #publish(
    publishAddress, payload
  ) {
    const address = publishAddress.replace('{$applicationId}', this.#id);
    this.#messageStream.publish(address, payload)
  }
}