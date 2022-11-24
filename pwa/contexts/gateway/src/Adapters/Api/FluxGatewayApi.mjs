import Actor from '../../Core/Actor.mjs';
import MessageStream from '../MessageStream/MessageStream.mjs';
import Definitions from '../Definitions/Definitions.mjs';

export default class FluxGatewayApi {
  /** @var {string} */
  #applicationName;
  /** @var {Actor} */
  #actor;
  /** @var {string} */
  #actorColor = '#C880B7'
  /** @var {MessageStream} */
  #messageStream;
  /** @var {Definitions} */
  #definitions;

  /**
   * @private
   */
  constructor(applicationName) {
    this.#applicationName = applicationName;
  }

  /**
   * @param {Config} config
   * @return {FluxGatewayApi}
   */
  static async initialize(config) {
    const obj = new FluxGatewayApi(
      config.applicationName
    );
    obj.#messageStream = await MessageStream.new(obj.#applicationName, config.logEnabled, obj.#actorColor);
    await obj.#initDefinitions(config.definitionsBaseUrl);
    await obj.#initOperations();
    return obj;
  }

  async #initDefinitions(definitionsBaseUrl) {
    this.#definitions = await Definitions.new(definitionsBaseUrl)
  }

  async initActor() {
    this.#actor = await Actor.new(this.#applicationName, (publishAddress, payload) => {
        this.#publish(
          publishAddress,
          payload
        )
      }
    );
  }

  async #initOperations() {
    const apiDefinition = await this.#definitions.apiDefinition();
    Object.entries(apiDefinition.operations).forEach(([operationId, operation]) => {
      const addressDef = operation.on.address;
      const address = addressDef.replace('{$applicationName}', this.#applicationName);
      this.#messageStream.register(address, (messagePayload) => {


        //const payload = messagePayloadData;
        let payload = {};
        payload =  operation.publishes.message.payload;

        if(messagePayload.data) {
          payload.data = messagePayload.data
        }


        console.log(payload);

        this.#handle(operation.handles,  operation.publishes.address, payload)
      })
    });
  }

  async #handle(command, publishTo, payload) {
    try {
      this.#actor[command](publishTo, payload);
    }
    catch (e) {
      console.error(command + " " + e)
    }
  }


  async #publish(
    publishAddress, payload
  ) {
    const address = publishAddress.replace('{$applicationName}', this.#applicationName);
    this.#messageStream.publish(address, payload)
  }
}