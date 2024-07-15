// markdown内の文字列を以下に置き換える
// (), (_) -> チェックされてないラジオボタン
// (x), (X) -> チェックされたラジオボタン
// [], [_] -> チェックされてないチェックボックス
// [x], [X] -> チェックされたチェックボックス

// usage
// md.use((md, options) => {
//     md.core.ruler.push("checkbox", thisfunc(md, options));
// })

// almost stolen from markdown-it-checkbox (https://github.com/mcecot/markdown-it-checkbox)
module.exports = function (md, options, Token) {
    "use strict";
    var arrayReplaceAt, createCheckBoxTokens, createRadioTokens, lastId, pattern4Checkbox, pattern4Radio, splitTextToken;
    arrayReplaceAt = md.utils.arrayReplaceAt;
    lastId = 0;
    pattern4Checkbox = /\[(X|\s|\_|\-)\]\s(.*)/i;
    pattern4Radio = /\((X|\s|\_|\-)\)\s(.*)/i;
    createRadioTokens = function (checked, label, Token) {
        var id, nodes, token;
        nodes = [];

        /**
         * <input type="checkbox" id="checkbox{n}" checked="true">
         */
        id = lastId;
        lastId += 1;
        token = new Token("checkbox_input", "input", 0);
        token.attrs = [["type", "radio"], ["id", id], ["onclick", "return false;"]];
        if (checked === true) {
            token.attrs.push(["checked", "true"]);
        }
        nodes.push(token);

        /**
         * <label for="checkbox{n}">
         */
        token = new Token("label_open", "label", 1);
        token.attrs = [["for", id]];
        nodes.push(token);

        /**
         * content of label tag
         */
        token = new Token("text", "", 0);
        token.content = label;
        nodes.push(token);

        /**
         * closing tags
         */
        nodes.push(new Token("label_close", "label", -1));
        return nodes;
    }
    createCheckBoxTokens = function (checked, label, Token) {
        var id, nodes, token;
        nodes = [];

        /**
         * <input type="checkbox" id="checkbox{n}" checked="true">
         */
        id = lastId;
        lastId += 1;
        token = new Token("checkbox_input", "input", 0);
        token.attrs = [["type", "checkbox"], ["id", id], ["onclick", "return false;"]];
        if (checked === true) {
            token.attrs.push(["checked", "true"]);
        }
        nodes.push(token);

        /**
         * <label for="checkbox{n}">
         */
        token = new Token("label_open", "label", 1);
        token.attrs = [["for", id]];
        nodes.push(token);

        /**
         * content of label tag
         */
        token = new Token("text", "", 0);
        token.content = label;
        nodes.push(token);

        /**
         * closing tags
         */
        nodes.push(new Token("label_close", "label", -1));
        return nodes;
    };
    splitTextToken = function (original, Token) {
        var checked, label, matches, text, value, isRadio;
        text = original.content;
        matches = text.match(pattern4Checkbox);
        if (matches === null) {
            matches = text.match(pattern4Radio);
            isRadio = true;
        }
        if (matches === null) {
            return original;
        }
        checked = false;
        value = matches[1];
        label = matches[2];
        if (value === "X" || value === "x") {
            checked = true;
        }
        return isRadio ? createRadioTokens(checked, label, Token) : createCheckBoxTokens(checked, label, Token);
    };
    return function (state) {
        var blockTokens, i, j, l, token, tokens;
        blockTokens = state.tokens;
        j = 0;
        l = blockTokens.length;
        while (j < l) {
            if (blockTokens[j].type !== "inline") {
                j++;
                continue;
            }
            tokens = blockTokens[j].children;
            i = tokens.length - 1;
            while (i >= 0) {
                token = tokens[i];
                blockTokens[j].children = tokens = arrayReplaceAt(tokens, i, splitTextToken(token, state.Token));
                i--;
            }
            j++;
        }
    };
};