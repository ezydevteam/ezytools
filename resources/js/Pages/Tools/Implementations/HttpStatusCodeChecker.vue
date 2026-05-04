<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        
        <div class="max-w-2xl mx-auto mb-8">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input 
                    v-model="searchQuery" 
                    type="text" 
                    class="block w-full pl-11 pr-4 py-4 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-lg shadow-sm" 
                    placeholder="Search for a status code (e.g. 404) or description (e.g. Not Found)..."
                >
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center" v-if="searchQuery">
                    <button @click="searchQuery = ''" class="text-surface-400 hover:text-surface-600 dark:hover:text-surface-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
            
            <div class="flex gap-2 mt-4 justify-center flex-wrap">
                <button @click="activeCategory = 'all'" :class="[activeCategory === 'all' ? 'bg-primary-600 text-white' : 'bg-surface-100 text-surface-700 dark:bg-surface-700 dark:text-surface-300 hover:bg-surface-200 dark:hover:bg-surface-600']" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors">All</button>
                <button @click="activeCategory = '1xx'" :class="[activeCategory === '1xx' ? 'bg-blue-600 text-white' : 'bg-surface-100 text-surface-700 dark:bg-surface-700 dark:text-surface-300 hover:bg-surface-200 dark:hover:bg-surface-600']" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors">1xx Informational</button>
                <button @click="activeCategory = '2xx'" :class="[activeCategory === '2xx' ? 'bg-green-600 text-white' : 'bg-surface-100 text-surface-700 dark:bg-surface-700 dark:text-surface-300 hover:bg-surface-200 dark:hover:bg-surface-600']" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors">2xx Success</button>
                <button @click="activeCategory = '3xx'" :class="[activeCategory === '3xx' ? 'bg-yellow-500 text-white' : 'bg-surface-100 text-surface-700 dark:bg-surface-700 dark:text-surface-300 hover:bg-surface-200 dark:hover:bg-surface-600']" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors">3xx Redirection</button>
                <button @click="activeCategory = '4xx'" :class="[activeCategory === '4xx' ? 'bg-orange-500 text-white' : 'bg-surface-100 text-surface-700 dark:bg-surface-700 dark:text-surface-300 hover:bg-surface-200 dark:hover:bg-surface-600']" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors">4xx Client Error</button>
                <button @click="activeCategory = '5xx'" :class="[activeCategory === '5xx' ? 'bg-red-600 text-white' : 'bg-surface-100 text-surface-700 dark:bg-surface-700 dark:text-surface-300 hover:bg-surface-200 dark:hover:bg-surface-600']" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors">5xx Server Error</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[600px] overflow-y-auto p-2">
            <div v-for="code in filteredCodes" :key="code.code" class="p-5 border border-surface-200 dark:border-surface-700 rounded-xl hover:shadow-md transition-shadow bg-surface-50 dark:bg-surface-900 group">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-3xl font-black" :class="getColorClass(code.code)">{{ code.code }}</span>
                    <span class="text-xs px-2 py-1 rounded-md bg-surface-200 dark:bg-surface-700 text-surface-700 dark:text-surface-300 font-medium whitespace-nowrap">{{ getCategoryName(code.code) }}</span>
                </div>
                <h4 class="text-lg font-bold text-surface-900 dark:text-white mb-2">{{ code.phrase }}</h4>
                <p class="text-sm text-surface-600 dark:text-surface-400 line-clamp-3 group-hover:line-clamp-none transition-all">{{ code.description }}</p>
            </div>
            
            <div v-if="filteredCodes.length === 0" class="col-span-full text-center py-12 text-surface-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-surface-300 dark:text-surface-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-lg">No HTTP Status Codes found matching "{{ searchQuery }}".</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const searchQuery = ref('');
const activeCategory = ref('all');

const getColorClass = (code) => {
    const c = parseInt(code);
    if (c >= 100 && c < 200) return 'text-blue-500';
    if (c >= 200 && c < 300) return 'text-green-500';
    if (c >= 300 && c < 400) return 'text-yellow-500';
    if (c >= 400 && c < 500) return 'text-orange-500';
    if (c >= 500 && c < 600) return 'text-red-500';
    return 'text-surface-500';
};

const getCategoryName = (code) => {
    const c = parseInt(code);
    if (c >= 100 && c < 200) return 'Informational';
    if (c >= 200 && c < 300) return 'Success';
    if (c >= 300 && c < 400) return 'Redirection';
    if (c >= 400 && c < 500) return 'Client Error';
    if (c >= 500 && c < 600) return 'Server Error';
    return 'Unknown';
};

const statusCodes = [
    { code: 100, phrase: "Continue", description: "The server has received the request headers and the client should proceed to send the request body." },
    { code: 101, phrase: "Switching Protocols", description: "The requester has asked the server to switch protocols and the server has agreed to do so." },
    { code: 200, phrase: "OK", description: "Standard response for successful HTTP requests. The actual response will depend on the request method used." },
    { code: 201, phrase: "Created", description: "The request has been fulfilled, resulting in the creation of a new resource." },
    { code: 202, phrase: "Accepted", description: "The request has been accepted for processing, but the processing has not been completed." },
    { code: 204, phrase: "No Content", description: "The server successfully processed the request and is not returning any content." },
    { code: 301, phrase: "Moved Permanently", description: "This and all future requests should be directed to the given URI." },
    { code: 302, phrase: "Found", description: "Tells the client to look at (browse to) another URL. 302 has been superseded by 303 and 307." },
    { code: 304, phrase: "Not Modified", description: "Indicates that the resource has not been modified since the version specified by the request headers." },
    { code: 307, phrase: "Temporary Redirect", description: "In this case, the request should be repeated with another URI; however, future requests should still use the original URI." },
    { code: 308, phrase: "Permanent Redirect", description: "The request and all future requests should be repeated using another URI." },
    { code: 400, phrase: "Bad Request", description: "The server cannot or will not process the request due to an apparent client error (e.g., malformed request syntax)." },
    { code: 401, phrase: "Unauthorized", description: "Similar to 403 Forbidden, but specifically for use when authentication is required and has failed or has not yet been provided." },
    { code: 403, phrase: "Forbidden", description: "The request was valid, but the server is refusing action. The user might not have the necessary permissions for a resource." },
    { code: 404, phrase: "Not Found", description: "The requested resource could not be found but may be available in the future. Subsequent requests by the client are permissible." },
    { code: 405, phrase: "Method Not Allowed", description: "A request method is not supported for the requested resource; for example, a GET request on a form that requires data to be presented via POST." },
    { code: 408, phrase: "Request Timeout", description: "The server timed out waiting for the request. The client may repeat the request without modifications at any later time." },
    { code: 409, phrase: "Conflict", description: "Indicates that the request could not be processed because of conflict in the request, such as an edit conflict." },
    { code: 418, phrase: "I'm a teapot", description: "The HTTP 418 I'm a teapot client error response code indicates that the server refuses to brew coffee because it is, permanently, a teapot." },
    { code: 422, phrase: "Unprocessable Entity", description: "The request was well-formed but was unable to be followed due to semantic errors (e.g. validation errors)." },
    { code: 429, phrase: "Too Many Requests", description: "The user has sent too many requests in a given amount of time (rate limiting)." },
    { code: 500, phrase: "Internal Server Error", description: "A generic error message, given when an unexpected condition was encountered and no more specific message is suitable." },
    { code: 502, phrase: "Bad Gateway", description: "The server was acting as a gateway or proxy and received an invalid response from the upstream server." },
    { code: 503, phrase: "Service Unavailable", description: "The server cannot handle the request (because it is overloaded or down for maintenance)." },
    { code: 504, phrase: "Gateway Timeout", description: "The server was acting as a gateway or proxy and did not receive a timely response from the upstream server." }
];

const filteredCodes = computed(() => {
    let result = statusCodes;
    
    if (activeCategory.value !== 'all') {
        const prefix = activeCategory.value.charAt(0);
        result = result.filter(c => c.code.toString().startsWith(prefix));
    }
    
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(c => 
            c.code.toString().includes(query) || 
            c.phrase.toLowerCase().includes(query) || 
            c.description.toLowerCase().includes(query)
        );
    }
    
    return result;
});
</script>
