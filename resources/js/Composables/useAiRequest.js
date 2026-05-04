import { ref } from 'vue';
import axios from 'axios';

export function useAiRequest() {
    const isLoading = ref(false);
    const error = ref(null);
    const result = ref('');
    const tokens = ref({ input: 0, output: 0 });
    const remainingRequests = ref(null);

    const generate = async (toolSlug, message, options = {}) => {
        isLoading.value = true;
        error.value = null;
        result.value = '';

        try {
            const response = await axios.post(`/api/ai/${toolSlug}`, {
                message,
                options
            });

            result.value = response.data.content;
            tokens.value = response.data.tokens;
            remainingRequests.value = response.data.remaining;
            
            return response.data;
        } catch (e) {
            error.value = e.response?.data?.message || e.message || 'An error occurred while generating the response.';
            if (e.response?.data?.remaining !== undefined) {
                remainingRequests.value = e.response.data.remaining;
            }
            throw e;
        } finally {
            isLoading.value = false;
        }
    };

    return {
        isLoading,
        error,
        result,
        tokens,
        remainingRequests,
        generate
    };
}
