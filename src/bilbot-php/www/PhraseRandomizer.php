<?php

namespace Bilbot;


class PhraseRandomizer
{
    public static function getRandomPhrase($key) {
        $phrases = [
            Constants::PHRASE_FALLBACK => [
                '😣 Lo siento, pero no he encuentro información relevante. ¿Puedes probar a preguntármelo de otro modo?',
                '😕 Por más que he buscado no logro encontrar nada útil, ¿Puedes decírmelo de otro modo?',
                '😞 Nada, prueba a decírmelo de otra manera a ver si encuentro algo por favor',
                '☹️ No encuentro nada, ¿qué tal si pruebas a cambiar tu frase?',
                '😓 Por más que busco no encuentro nada, ¿puedes decírmelo de otro modo?',
            ],
            Constants::PHRASE_LONGER => [
                '😕 Necesito una frase más larga para poder buscar mejor la información',
                '🙁 Lo siento, pero necesito frases un poco más largas para poder hacer mi trabajo',
                '😔 ¿Puedes escribirme frases un poquito más largas?',
                '😖 Algo ha ido mal, creo que necesito frases un poco más largas para funcionar bien',
                '😑 Necesito que me escribas frases un poquito más largas para funcionar como debo',
            ],
            Constants::PHRASE_EMOTION_POSITIVE => [
                '😃 ¡Buenas noticias! ',
                '🤠 ¡Yey!',
                '🤓 ¡Genial!',
                '😁 ¡Bingo!',
                '😎 ¡Perfecto!',
            ],
            Constants::PHRASE_EMOTION_NEGATIVE => [
                '😔 sé que a veces tardo un poco ',
                'Intento ser lo más servicial que puedo 😞  ',
                'Todavía me queda bastante que mejorar 😕 ',
                'Voy lo más rápido que puedo 😓 ',
                'A veces es complicado 😣 ',
            ],
            Constants::PHRASE_RESULTS_FOUND => [
                ', aquí tienes una selección de resultados',
                ', aquí tienes mis recomendaciones',
                ', estos son unos resultados que pueden ser de tu interés',
                ', aquí tienes unos cuantos resultados',
                ', aquí tienes una lista',
            ],
            Constants::PHRASE_RESULTS_SPECIFIC_FOUND => [
                ', aquí tienes lo que he encontrado',
                ', he encontrado estos resultados',
                ', creo que esto puede ser de utilidad',
                ', creo que esto puede serte de ayuda',
                ', todo esto te puede interesar',
            ],
            Constants::PHRASE_ERROR => [
                '😣 ¡Ups! Ha habido un problema y no puedo mostrarte esta información, ¿puedes probar en otro momento?',
                '😕 Vaya... algo ha salido mal, ¿puedes intentarlo más tarde?',
                '😟 Vaya... algo no está como debería, juraría que esto antes funcionaba, ¿puedes probar luego? ',
                '😖 ¡Ains! Esto es embarazoso, algo ha salido mal, ¿puedes probar más tarde?',
            ],
            Constants::PHRASE_GREETING => [
                'Espero haberte sido de ayuda 😉',
                'Siempre a tu servicio 😀',
                '¡Gracias por confiar en mí! ¡Espero haberte sido útil! 😁',
                'Espero haberte ayudado, ¡Pasa un buen día! 🙃',
                'Gracias por usar mis servicios ☺️',
            ],
            Constants::PHRASE_EMOTION_NEUTRAL => [
                'Mira ',
                'Aqui tienes ',
            ],
            Constants::PHRASE_RESULTS_SPECIFIC_CONNECTOR => [
                'con respecto a ',
                'sobre ',
            ],
            Constants::PHRASE_REQUEST_LOCATION => [
                'Envíame tu posición',
                'Si me mandas tu posición puedo ayudarte a buscar alguno cercano',
            ],
            Constants::PHRASE_NOT_FOUND_WITH_LOCATION => [
                'No he encontrado puntos en medio kilómetro a la redonda...',
                'No tienes puntos de recogida cerca',
                'Parece que no estás cerca de ningún punto de recogida',
            ],
            Constants::PHRASE_FOUND_WITH_LOCATION => [
                'Mira los puntos que he encontrado en menos de 500 metros ',
                'Tienes los siguientes puntos cerca ',
                'Tienes estos puntos cercanos  ',
            ],
            Constants::PHRASE_GREETINGS_COMMAND => [
                '👋 ¡Hola! Soy Bilbot, sé un montón de cosas sobre Bilbao, mira mis comandos y pregunta lo que quieras',
                '¡Buenas! Me llamo Bilbot, y te puedo ayudar a moverte por Bilbao, echa un ojo a mis comandos y pregúntame lo que quieras',
                'Hola, soy Bilbot, se muchas cosas de Bilbao y estoy aquí para echarte una mano, pregúntame lo que quieras usando mis comandos',
            ]
        ];

        return $phrases[$key][array_rand($phrases[$key])];
    }
}