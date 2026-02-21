(function (blocks, editor, components, element) {
    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var TextControl = components.TextControl;
    var decodeEntities = wp.htmlEntities.decodeEntities;

    registerBlockType('custom-blocks/youtube', {
        title: 'YouTube Video',
        icon: 'shield',
        category: 'common',
        attributes: {
            youtubeID: {
                type: 'string',
                default: '',
            },
        },
        edit: function (props) {
            var setAttributes = props.setAttributes;

            function onChangeYoutubeID(newValue) {
                setAttributes({ youtubeID: newValue });
            }

            return el(
                'div',
                {},
                el(
                    TextControl,
                    {
                        label: 'YouTube Video ID',
                        value: decodeEntities(props.attributes.youtubeID),
                        onChange: onChangeYoutubeID,
                    }
                )
            );
        },
        save: function (props) {
            return el(
                'div',
                {},
                el('lite-youtube', { videoid: props.attributes.youtubeID, playlabel: 'Play' })
            );
        },
    });
})(
    window.wp.blocks,
    window.wp.editor,
    window.wp.components,
    window.wp.element
);
