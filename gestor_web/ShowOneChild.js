class ShowOneChild {
    constructor(storage) {
        this.storage = storage;
    }

    readRenderPromptFromStorage() {
        if (this.storage && typeof this.storage.getWithTTL === 'function') {
            // Lógica para leer el prompt desde el almacenamiento
            return this.storage.getWithTTL('renderPrompt');
        } else {
            console.error("El objeto storage no está definido o no contiene el método getWithTTL");
            return null;
        }
    }

    getRenderPrompt() {
        const prompt = this.readRenderPromptFromStorage();
        if (prompt) {
            // Lógica para manejar el prompt
            console.log("Prompt obtenido:", prompt);
        } else {
            console.warn("No se pudo obtener el prompt");
        }
    }

    render() {
        this.getRenderPrompt();
        // Lógica adicional para renderizar
        console.log("Renderizando ShowOneChild...");
    }
}

// Ejemplo de uso
const storageMock = {
    getWithTTL: (key) => {
        if (key === 'renderPrompt') {
            return "Este es un prompt de ejemplo";
        }
        return null;
    }
};

const showOneChild = new ShowOneChild(storageMock);
showOneChild.render();