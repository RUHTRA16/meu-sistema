
import { marked } from 'marked';
import DOMPurify from 'dompurify';

// Configura a interpretação do Markdown.
marked.setOptions({
    gfm: true,
    breaks: true,
});

// Disponibiliza um formatador seguro para o chat.
window.formatarRespostaMestre = (texto) => {
    const markdown = marked.parse(texto);
    return DOMPurify.sanitize(markdown);
};

