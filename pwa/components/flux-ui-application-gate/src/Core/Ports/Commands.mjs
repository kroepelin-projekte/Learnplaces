export class Command {
  static getName() {
    return this.constructor.name
  }
}


export class Create extends Command {
  static create(componentId) {
    return {componentId: componentId}
  }
}
export class InitializeLayoutContext extends Command {
  static create(srcApi) {
    return {srcApi: srcApi}
  }
}