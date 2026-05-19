package testMp4Parser;

import java.io.ByteArrayInputStream;
import java.io.DataInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.URL;
import java.net.URLConnection;
import java.nio.ByteBuffer;
import java.nio.channels.WritableByteChannel;
import java.util.Arrays;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Document;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.xml.sax.SAXException;

import com.coremedia.iso.IsoFile;
import com.coremedia.iso.boxes.UserBox;
import com.googlecode.mp4parser.DataSource;
import com.googlecode.mp4parser.util.Path;

public class TestMp4Parser {
    public static void main(String[] args) throws IOException, SAXException, ParserConfigurationException {
         checkSpecialMetadata("http://static.sapsailing.com/V0090010.MP4");
    }

    /**
     * Only keep the metadatarelevant parts of the file, discard the rest for constant memory footprint
     */
    static class PseudoLocalFile implements DataSource {
        byte[] startOfFile = new byte[1024];
        byte[] endOfFile = new byte[1024*256];
        private long position;
        private long size;

        public PseudoLocalFile(URL input) throws IOException {
            downloadSparse(input);
        }

        private void downloadSparse(URL input) throws IOException {
            System.out.println("Downloading start of " + input + " " + startOfFile.length + " bytes");
            URLConnection connection = input.openConnection();
            size = connection.getContentLengthLong();
            float lastPercentNotification = 0;
            byte[] transferBuffer = new byte[1024];
            try (InputStream urlIn = input.openStream()) {
                DataInputStream bin = new DataInputStream(urlIn);
                long offset = 0;
                while (offset < size) {
                    float progress = (float) offset / size;
                    if(progress > lastPercentNotification+0.1f){
                        System.out.println("download progress " + Math.round(progress*100));
                        lastPercentNotification = progress;
                    }
                    bin.readFully(transferBuffer, 0, (int) Math.min(size - offset, transferBuffer.length));
                    if (offset < startOfFile.length) {
                        System.arraycopy(transferBuffer, 0, startOfFile, (int) offset, transferBuffer.length);
                    }
                    if (offset + transferBuffer.length > size - endOfFile.length) {
                        long startOfEndBuffer = size - endOfFile.length - 1;
                        int relativeOffset = (int) (offset - startOfEndBuffer);
                        int remain = endOfFile.length - relativeOffset - 1;
                        int skipToStartOfEndbuffer = relativeOffset > 0 ? 0 : -relativeOffset;
                        int toCopyLength = transferBuffer.length - skipToStartOfEndbuffer;
                        System.arraycopy(transferBuffer, skipToStartOfEndbuffer, endOfFile,
                                relativeOffset < 0 ? 0 : relativeOffset, Math.min(remain, toCopyLength));
                    }
                    offset += transferBuffer.length;
                }
            }
        }

        @Override
        public int read(ByteBuffer byteBuffer) throws IOException {
            long start = position;
            int toRead = byteBuffer.remaining();
            long end = start + toRead;
            long endOfFileBufferOffset = size - endOfFile.length - 1;
            if (end < startOfFile.length) {
                // This is safe, as we only handle the first few megabytes here, this should never be over 4gb!
                byte[] slice = Arrays.copyOfRange(startOfFile, (int) start, (int) end);
                byteBuffer.put(slice);
                position += toRead;
                return toRead;
            } else if (start > endOfFileBufferOffset) {
                long relativeStart = position - (endOfFileBufferOffset);
                long relativeEnd = relativeStart + toRead;
                byte[] slice2 = Arrays.copyOfRange(endOfFile, (int) relativeStart, (int) relativeEnd);
                byteBuffer.put(slice2);
                position += toRead;
                return toRead;
            } else {
                throw new RuntimeException("Out of mapped range access!!");
            }
        }

        @Override
        public long position() throws IOException {
            return position;
        }

        @Override
        public void position(long nuPos) throws IOException {
            position = nuPos;
        }

        @Override
        public long size() throws IOException {
            return size;
        }

        @Override
        public long transferTo(long position, long count, WritableByteChannel target) throws IOException {
            throw new RuntimeException("Not implemented");
        }

        @Override
        public ByteBuffer map(long startPosition, long size) throws IOException {
            throw new RuntimeException("Not implemented");
        }

        @Override
        public void close() throws IOException {

        }

    }

    private static void checkSpecialMetadata(String string)
            throws IOException, ParserConfigurationException, SAXException {
        URL input = new URL(string);

        IsoFile isof = new IsoFile(new PseudoLocalFile(input));
        // MovieHeaderBox movieHeaderBox = Path.getPath(isof, "moov[0]/mvhd");
        // System.out.println(movieHeaderBox.getCreationTime());

        UserBox uuidBox = Path.getPath(isof, "moov[0]/trak[0]/uuid");

        DocumentBuilderFactory dbFactory = DocumentBuilderFactory.newInstance();
        DocumentBuilder dBuilder = dbFactory.newDocumentBuilder();
        Document doc = dBuilder.parse(new ByteArrayInputStream(uuidBox.getData()));

        NodeList childs = doc.getDocumentElement().getChildNodes();
        for (int i = 0; i < childs.getLength(); i++) {
            Node child = childs.item(i);
            if (child.getNodeName().toLowerCase().contains(":spherical")) {
                System.out.println(child.getNodeName());
                System.out.println(child.getTextContent());
            }
        }
    }
}
